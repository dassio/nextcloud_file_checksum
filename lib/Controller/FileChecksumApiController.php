<?php

namespace OCA\FileChecksum\Controller;

use OCA\Files_External\Config\ExternalMountPoint;
use OCP\IRequest;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\ApiController;
use OCP\Files\IRootFolder;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\FIles\Node;

class FileChecksumApiController extends ApiController
{

	/** @var string */
	private $userId;
	/** @var IRootFolder */
	private $rootFolder;

	public function __construct($AppName, IRequest $request, string $userId, IRootFolder $rootFolder)
	{
		parent::__construct($AppName, $request);
		$this->userId = $userId;
		$this->rootFolder = $rootFolder;
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @param string $folder
	 *
	 * getting all files checksum information
	 */
	public function  getChecksumStatistic(string $folder): JSONResponse
	{
		if ($folder == 'root') {
			$userFolder = $this->rootFolder->getUserFolder($this->userId);
		} else {
			$userFolder = $this->rootFolder->getPath($folder);
		}

		$data = $this->scanCurrentFolderFiles($userFolder);
		$result = $this->formatData($data);

		return new JSONResponse($result, Http::STATUS_OK);
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
					yield $subnode;
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
?>