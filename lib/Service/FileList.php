<?php
namespace OCA\FileChecksum\Service;

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
    /** @var int */
    private $file_count;
    /** @var string */
    private $file_count_temp_file;
    /** @var string */
    private $json_result_temp_file;
   


    public function __construct(ITimeFactory $time, IRootFolder $rootFolder) {
        parent::__construct($time);
        $this->rootFolder = $rootFolder;
    }

    protected function run($arguments) {
        $folder = $arguments['folder'];
        $this->userId = $arguments['uid'];
        $this->file_count_temp_file = $arguments['file_count_temp_file'];
        $this->json_result_temp_file = $arguments['json_result_temp_file'];


        if ($folder == 'root') {
			$userFolder = $this->rootFolder->getUserFolder($this->userId);
		} else {
			$userFolder = $this->rootFolder->getPath($folder);
		}

        $data = $this->scanCurrentFolderFiles($userFolder);
        file_put_contents($this->file_count_temp_file,$this->file_count . ":done");
        $result = $this->formatData($data);
        file_put_contents($this->json_result_temp_file,json_encode($result));
    }


    /**
	 * @param string $folder
	 */
	private function scanCurrentFolderFiles(Folder $folder): iterable
	{
		$nodes = $folder->getDirectoryListing();

		foreach ($nodes as $node) {
			if ($node instanceof Folder) {
				foreach ( $this->scanCurrentFolderFiles($node) as $subnode) {
                    if ($subnode instanceof File){
                        yield $subnode;
                        $this->file_count = $this->file_count + 1;
                    }
				}
			} elseif ($node instanceof File) {
                yield $node;
                $this->file_count = $this->file_count + 1;
			}
		}
    }
    
    private function logScannedFiles(){
        if($this->file_count % 100 == 0){
            file_put_contents($this->file_count_temp_file,$this->file_count . ":not_done");
        }
    }


	private function formatData(iterable $nodes): array
	{
		$userFolder = $this->rootFolder->getUserFolder($this->userId);

		$result = [];
		/** @var Node $node */
		foreach ($nodes as $node) {
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