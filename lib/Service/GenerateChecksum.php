<?php
namespace OCA\FilesChecksum\Service;

use \Exception;

use OCP\AppFramework\Utility\ITimeFactory;
use \OCP\BackgroundJob\QueuedJob;

use OCP\Files\IRootFolder;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\FilesChecksum\Db\ScannedFileMapper;
use OCA\FilesChecksum\Db\ScannedFile;

class GenerateChecksum extends QueuedJob
{

  /** @var string */
  private $userId;
  /** @var IRootFolder */
  private $rootFolder;
  /** @var IDBConnection */
  private $db;

  private $scannedMapper;


  public function __construct(
    ITimeFactory $time,
    IRootFolder $rootFolder,
    IDBConnection $db,
    ScannedFileMapper $scannedMapper
  )
  {
    parent::__construct($time);
    $this->rootFolder = $rootFolder;
    $this->db = $db;
    $this->scannedMapper = $scannedMapper;
  }

  /**
   *Main Funtion
   *@return null
   **/
  protected function run($arguments)
  {
    $this->userId = $arguments['uid'];

    try {
      $scannedFiles = $this->scannedMapper->findAll($this->userId);

      foreach ($scannedFiles as $file) {
        $fileId = $file->getId();
        if ($fileId < 0 ) {
          continue;
        }
        //mark file as caculating SHA1, some files need to download first
        //and it will take a long time
        //also for status check
        $this->updateChecksum($file, "caculating");
        
        $tempFile = $this->copyFile($fileId);
        $this->updateChecksum( $file, sha1_file($tempFile) );
        $this->updatePhotoMetadata($tempFile,$file);
        echo "updated file checksum : " . $file->getFileName() . "-".sha1_file($tempFile) . "\n";
        unlink($tempFile); 
      }
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }
  
  /**
   * copy file to temp file for operation 
   * @return string
   */
  private function copyFile(int $fileId): string{
    $userFolder = $this->rootFolder->getUserFolder($this->userId);
    $file = $userFolder->getById($fileId)[0];
    $fileStorage = $file->getStorage();
    $sourceStream = $fileStorage->fopen($file->getInternalPath(),'r');
    $temp = tempnam('/tmp/', 'nextcloud_files_checksum');
    $destStream = fopen($temp,'w');
    stream_copy_to_stream($sourceStream,$destStream);
    return $temp;
  }

  /**
   * save photo metadata in the app database
   * */
  private function updatePhotoMetadata(String $tempFile,ScannedFile $scannedFile) {
    if(strpos($scannedFile->getFileName(),".jpg") !== FALSE or strpos($scannedFile->getFileName(),".jpg") !== FALSE){
      $metadata = exif_read_data($tempFile);
      unset($metadata['UserComment']);
      $metadata = json_encode($metadata);
      $scannedFile->setMetadata($metadata);
      $this->scannedMapper->update($scannedFile);

    }
  }


  /**
   * update checksum in DB
   */
  private function updateChecksum(ScannedFile $file, String $fileSha1)
  {
    $fileId = $file->getId();
    $userFolder = $this->rootFolder->getUserFolder($this->userId); 
    $realFile = $userFolder->getById($fileId)[0];
    $fileSize = $realFile->getSize();
    if($fileSha1 == "da39a3ee5e6b4b0d3255bfef95601890afd80709" and $fileSize != 0){
      throw new Exception("file not empty but checksum is of empty file");
    }

    if (!$this->hasChecksum($fileId)) {
      $qb = $this->db->getQueryBuilder();

      $qb->update('filecache')
         ->set('checksum', $qb->createNamedParameter("SHA1:" . $fileSha1))
         ->where(
           $qb->expr()->eq('fileid', $qb->createNamedParameter($fileId, IQueryBuilder::PARAM_INT))
         );
      $cursor = $qb->execute();
    }
  }

  /**
   * check if checksum already exist
   */
  private function hasChecksum(int $fileId): bool
  {
    $qb = $this->db->getQueryBuilder();

    $qb->select('checksum')
       ->from('filecache')
       ->where(
         $qb->expr()->eq('fileid', $qb->createNamedParameter($fileId, IQueryBuilder::PARAM_INT))
       );
    $cursor = $qb->execute();
    $row = $cursor->fetch();
    $cursor->closeCursor();
    if ($row['checksum'] == '' or $row['checksum'] == 'SHA1:caculating' or $row['checksum'] == "SHA1:da39a3ee5e6b4b0d3255bfef95601890afd80709"){
      return false;
    }else{
      return true;
    }
  }
}
