<?php
namespace OCA\FileChecksum\Service;

use \Exception;

use OCP\AppFramework\Utility\ITimeFactory;
use \OCP\BackgroundJob\QueuedJob;

use OCA\Files_External\Config\ExternalMountPoint;
use OCP\Files\IRootFolder;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\FIles\Node;

class FileList extends QueuedJob {

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
   


    public function __construct(ITimeFactory $time, IRootFolder $rootFolder) {
        parent::__construct($time);
		$this->rootFolder = $rootFolder;
		$this->file_count = 0;
    }

	/**
	 * Main Funtion 
	 */
    protected function run($arguments) {
		try{
			$folder = $arguments['folder'];
			$this->userId = $arguments['uid'];
			$this->json_result_temp_file = $arguments['json_result_temp_file'];
			$this->file_count_temp_file = $arguments['file_count_temp_file'];
	
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
        
		$result = $this->formatData($data);
		$result[] = ["scanning_finished"=>"YES"];
		file_put_contents($this->json_result_temp_file,json_encode($result));
		file_put_contents($this->file_count_temp_file,$this->file_count . ":finished");
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
				file_put_contents($this->file_count_temp_file,$this->file_count . ":not_finished");
				foreach ( $this->scanCurrentFolderFiles($node) as $subnode) {
                    if ($subnode instanceof File){
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

			$result[] = [
				'basename' => $isRoot ? '' : $node->getName(),
				'fileid' => $node->getId(),
				'filename' => $path,
				'size' => $node->getSize(),
				'type' => $node->getType(),
				'external' => $external
			];
		}
		return $result;
	}

}