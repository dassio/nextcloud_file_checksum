<?php

namespace OCA\FileChecksum\Controller;

use OCA\Files_External\Config\ExternalMountPoint;
use OCP\IRequest;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;

use OCP\Files\IRootFolder;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\FIles\Node;

class HomeController extends Controller
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
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index()
	{
		return new TemplateResponse('files_checksum', 'index');  // templates/index.php
	}
}
