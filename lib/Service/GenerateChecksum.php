<?php
namespace OCA\FilesChecksum\Service;

use \Exception;

use OCP\AppFramework\Utility\ITimeFactory;
use \OCP\BackgroundJob\QueuedJob;

use OCA\Files_External\Config\ExternalMountPoint;
use OCP\Files\IRootFolder;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\Node;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;


class GenerateChecksum extends QueuedJob {

  /** @var string */
  private $userId;
  /** @var IRootFolder */
  private $rootFolder;
  /** @var IDBConnection */
  private $db;


  public function __construct(
    ITimeFactory $time,
    IRootFolder $rootFolder,
    IDBConnection $db)
  {
    parent::__construct($time);
    $this->rootFolder = $rootFolder;
    $this->db = $db;
  }

  /**
   *    * Main Funtion
   *       */
  protected function run($arguments){
    $this->userId = $arguments['uid'];
    $this->json_result_temp_file = $arguments['json_result_temp_file'];

    try {
      $jsonData = file_get_contents($this->json_result_temp_file);
      $json = json_decode($jsonData, true);

      $userFolder = $this->rootFolder->getUserFolder($this->userId);


      foreach ($json as $file){
        $fileId = $file['fileid'];
        //mark file as caculating SHA1, some files need to download first
        //and it will take a long time
        //also for status check
        $this->updateChecksum($fileId,"caculating");
        //caculating  the real SHA1
        $file = $userFolder->getById($fileId)[0];
        $fileSha1 = generateSha1($file);
        $this->updateChecksum($fileId,$fileSha1);
        echo "updated file checksum : " . $file->getName() . "-".$fileSha4 . "\n";
      }
    }catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  private function generateSha1(File $file): String {
    $temp = tempnam('/tmp/','nextcloud_files_checksum');
    $file.copy($temp);
    $fileSha1 = sha1_file($temp);
    unlink($temp);

    return $fileSha1;
  }

  /** 
   * update checksum in DB
   */
  private function updateChecksum(String $fileId,String $fileSha1){
    if (!checkChecksum()){
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
  private function checkChecksum(String $fileId): bool {
    $qb = $this->db->getQueryBuilder();

    $qb->select('checksum')
       ->from('filecache')
       ->where(
         $qb->expr()->eq('fileid', $qb->createNamedParameter($fileId, IQueryBuilder::PARAM_INT))
       );
    $cursor = $qb->execute();
    $row = $cursor->fetch();;
	  $cursor->closeCursor();
    return $row;
  }
}
