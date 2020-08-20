<?php
namespace OCA\FilesChecksum\Service;

use \Exception;

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\QueuedJob;

use OCA\Files_External\Config\ExternalMountPoint;
use OCP\Files\IRootFolder;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\Node;

use OCA\FilesChecksum\Db\ScannedFileMapper;
use OCA\FilesChecksum\Db\ScannedFile;

class FileList extends QueuedJob
{

  /** @var string */
    private $userId;
    /** @var IRootFolder */
    private $rootFolder;
    /** @var string */
    private $json_result_temp_file;
    /** @var string */
    private $file_count_temp_file;
    /** @var int */
    private $file_count;
    private $scannedMapper;



    public function __construct(ITimeFactory $time, IRootFolder $rootFolder, ScannedFileMapper $scannedMapper)
    {
        parent::__construct($time);
        $this->rootFolder = $rootFolder;
        $this->file_count = 0;
        $this->scannedMapper = $scannedMapper;
    }

    /**
     * Main Funtion
     */
    protected function run($arguments)
    {
        try {
            $folder = $arguments['folder'];
            $this->userId = $arguments['uid'];

            echo "start scanning for user" . "\n";

            if ($folder == 'root') {
                $userFolder = $this->rootFolder->getUserFolder($this->userId);
            } else {
                $userFolder = $this->rootFolder->getPath($folder);
            }

            $data = $this->scanCurrentFolderFiles($userFolder);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        //using specical file with file_id=-1 for progress tracking
        if (!($this->hasProgressFile())) {
            $this->addProgressFile();
        }

        $result = $this->formatData($data);
        $this->saveScannedFile($result);
        $this->updateProgress(true);
    }

    /**
     * @param string $folder
     */
    private function scanCurrentFolderFiles(Folder $folder): iterable
    {
        $nodes = $folder->getDirectoryListing();

        foreach ($nodes as $node) {
            if ($node instanceof Folder) {
                echo "start scanning folder : " . $node->getPath() . " : " . $this->file_count . "\n";
                $this->updateProgress(false);
                foreach ($this->scanCurrentFolderFiles($node) as $subnode) {
                    if ($subnode instanceof File) {
                        yield $subnode;
                    }
                }
            } elseif ($node instanceof File) {
                yield $node;
            }
        }
    }

    private function formatData(iterable $nodes): array
    {
        $userFolder = $this->rootFolder->getUserFolder($this->userId);

        $result = [];
        /** @var Node $node */
        foreach ($nodes as $node) {
            $this->file_count++;
            $isRoot = $node === $userFolder;
            $external = $node->getMountPoint() instanceof ExternalMountPoint;
            $path = $userFolder->getRelativePath($node->getPath());
            $hasChecksum = $node->getChecksum() != "";

            $result[] = [
        'basename' => $isRoot ? '' : $node->getName(),
        'fileid' => $node->getId(),
        'filename' => $path,
        'size' => $node->getSize(),
        'type' => $node->getType(),
        'external' => $external,
        'hasChecksum' => $hasChecksum,
      ];
        }
        return $result;
    }

    private function saveScannedFile(array $result)
    {
        $time = new \DateTime("now");
        $datetime = $time->getTimestamp();
        foreach ($result as $item) {
            $file = new ScannedFile();
            $file->setId($item['fileid']);
            $file->setFileName($item['filename']);
            $file->setBaseName($item['basename']);
            $file->setType($item['type']);
            $file->setExternal($item['external']);
            $file->setUserId($this->userId);
            $file->setTime($datetime);
            $this->scannedMapper->insert($file);
        }
    }

    /**
     * using file id -1 for progress monitor
     *
     **/
    private function addProgressFile()
    {
        $datetime = new \DateTime("now");
        $datetime = $datetime->getTimestamp();

        $finishedFile = new ScannedFile();
        $finishedFile->setId(-1);
        $finishedFile->setFileName("test");
        $finishedFile->setBaseName("test");
        $finishedFile->setType("finishedFile");
        $finishedFile->setExternal(false);
        $finishedFile->setUserId($this->userId);
        $finishedFile->setTime($datetime);
        $finishedFile->setFinished(false);
        $finishedFile->setScannedNum(0);

        $this->scannedMapper->insert($finishedFile);
    }
    private function hasProgressFile(): bool
    {
        $result = $this->scannedMapper->find(-1, $this->userId);
        if ($result == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * using special file with file_id=-1 for progress tracking
     * using basename for scanned files number tracking
     * */
    private function updateProgress(bool $finished)
    {
        $file = $this->scannedMapper->find(-1, $this->userId);
        $file->setFinished($finished);
        $file->setScannedNum($this->file_count);
        $this->scannedMapper->update($file);
    }
}
