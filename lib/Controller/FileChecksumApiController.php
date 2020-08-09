<?php

namespace OCA\FileChecksum\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\ApiController;
use OCP\Files\IRootFolder;
use \OCP\BackgroundJob\IJobList;
use OCP\ISession;

use OCA\FileChecksum\Service\FileList;

class FileChecksumApiController extends ApiController
{

	/** @var string */
	private $userId;
	/** @var IJobList */
	private $jobList;
	/** @var ISession */
	private $session;

	public function __construct($AppName, IRequest $request, 
								ISession $session,
								string $userId, 
								IJobList $jobList)
	{
		parent::__construct($AppName, $request);
		$this->userId = $userId;
		$this->jobList = $jobList;
		$this->session = $session;
		$this->session['file_count_temp_file'] = tempnam(sys_get_temp_dir(), 'nextcloud_filechecksum_count');
		$this->session['json_result_temp_file'] = tempnam(sys_get_temp_dir(), 'nextcloud_filechecksum_result');

	}

	/**
	 * @NoAdminRequired
	 * @param string $folder
	 *
	 * getting all files checksum information
	 */
	public function  getChecksumStatistic(string $folder): JSONResponse
	{
		$this->jobList->add(FileList::class, 
							['uid' => $this->userId,
							'folder'=>$folder,
							'file_count_temp_file'=>$this->session['file_count_temp_file'],
							'json_result_temp_file'=>$this->session['json_result_temp_file']
							]);
		// putenv("SHELL=/bin/bash");
		// print `echo /usr/bin/php -q /var/www/html/cron.php | at now 2>&1`;
		exec('/usr/local/bin/php -q /var/www/html/cron.php  > /dev/null 2>&1 &');

		$result[] = ["status"=>"submit_ok"];
		return new JSONResponse($result, Http::STATUS_OK);
	}
}
?>