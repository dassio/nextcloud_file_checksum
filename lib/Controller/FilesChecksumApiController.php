<?php

namespace OCA\FilesChecksum\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\ApiController;
use OCP\Files\IRootFolder;
use OCP\BackgroundJob\IJobList;
use OCP\ISession;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCA\FilesChecksum\Service\FileList;
use OCA\FilesChecksum\Service\GenerateChecksum;
use OCA\FilesChecksum\Db\ScannedFileMapper;
use \OCP\AppFramework\Db\DoesNotExistException;

class FilesChecksumApiController extends ApiController
{

  /** @var string */
    private $userId;
    /** @var IJobList */
    private $jobList;
    /** @var ISession */
    private $session;
    /** @var IRootFolder */
    private $rootFolder;
    private $db;
    private $scannedMapper;

    public function __construct(
        $AppName,
        IRequest $request,
        ISession $session,
        string $userId,
        IJobList $jobList,
        IRootFolder $rootFolder,
        IDBConnection $db,
        ScannedFileMapper $scannedMapper
    ) {
        parent::__construct($AppName, $request);
        $this->userId = $userId;
        $this->jobList = $jobList;
        $this->session = $session;
        $this->rootFolder = $rootFolder;
        $this->db = $db;
        $this->scannedMapper = $scannedMapper;

        if (!$this->session->exists('proccesss_output_temp_file')) {
            $this->session['proccesss_output_temp_file'] = tempnam(sys_get_temp_dir(), 'nextcloud_proccesss_output_temp_file-'.$this->userId);
        }
    }

    /**
     * @NoAdminRequired
     * @param string $folder
     * @UseSession
     *
     * getting all files checksum information
     */
    public function startScanning(string $folder): JSONResponse
    {
        if (!$this->checkFinshedLastRun) {
            $this->jobList->add(
                FileList::class,
                ['uid' => $this->userId,
        'folder'=>$folder,
        ]
            );
            $command = 'nohup /usr/local/bin/php -q /var/www/html/apps/files_checksum/lib/Command/cron.php  > '. $this->session['proccesss_output_temp_file'] . ' 2>&1 & echo $!;';
            $pid = exec($command, $output);
            $this->session->set("pid", (int)$output[0]);
        }

        $result[] = ["status"=>"submit_ok"];
        return new JSONResponse($result, Http::STATUS_OK);
    }

    /**
     * @NoAdminRequired
     * @param string $folder
     * @UseSession
     *
     * getting all files checksum information
     */
    public function restartScanning(string $folder): JSONResponse
    {
        $this->emptyScannedFile();
        return $this->startScanning($folder);
    }

    /**
     * @NoAdminRequired
     * @param string $folder
     *
     * getting all files checksum information
     */
    public function cancelScanning()
    {
        exec("kill -9 ".$this->session['pid']);

        $this->emptyScannedFile();
        $result[] = ["status"=>"submit_ok"];
        return new JSONResponse($result, Http::STATUS_OK);
    }

    /**
     * @NoAdminRequired
     * Check scanning progress
     */
    public function getChecksumStatisticStatus(): JSONResponse
    {
        $scannedFileNum = $this->getScannedFileNum();
        $progress = $this->checkScanFinished() ? "finished" : "not_finished";
        $result[] = ["fileNum"=>$scannedFileNum,"progress"=>$progress];
        return new JSONResponse($result, Http::STATUS_OK);
    }

    /**
     * @NoAdminRequired
     * get final file list
     */
    public function getChecksumStatistic(): JSONResponse
    {
        $finished = $this->checkScanFinished();
        if ($finished) {
            $files = $this->scannedMapper->findAll($this->userId);
            $json = json_encode($files);
            return new JSONResponse($json, Http::STATUS_OK);
        } else {
            $result[] = ["error"=>"sanning not finished"];
            return new JSONResponse($result, Http::STATUS_OK);
        }
    }

    /**
     * @NoAdminRequired
     * @UseSession
     * generate file checksum
     */
    public function generateChecksum(): JSONResponse
    {
        $this->jobList->add(
            GenerateChecksum::class,
            ['uid' => $this->userId,
      'json_result_temp_file'=>$this->session['json_result_temp_file']
      ]
        );

        $command = 'nohup /usr/local/bin/php -q /var/www/html/apps/files_checksum/lib/Command/cron.php  > '. $this->session['proccesss_output_temp_file'] . ' 2>&1 & echo $!;';

        $pid = exec($command, $output);
        $this->session->set("checksum_pid", (int)$output[0]);

        $result[] = [ "status"=>"OK" ];
        return new JSONResponse($result, Http::STATUS_OK);
    }


    /**
     * Check if last run finished scanning
     */
    private function checkFinshedLastRun(): bool
    {
        $jsonData = file_get_contents($this->session['json_result_temp_file']);
        $json = json_decode($jsonData, true);
        if (array_key_exists('scanning_finished', $json)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * delete scanned files that belong to one user
     */
    private function emptyScannedFile()
    {
        try {
            $files = $this->scannedMapper->findAll($this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        foreach ($files as $file) {
            $this->scannedMapper->delete($file);
        }
    }

    private function checkScanFinished(): bool
    {
        $progressFile = $this->scannedMapper->find(-1, $this->userId);
        if ($progressFile == null) {
            return false;
        } else {
            if ($progressFile->getFinished()) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function getScannedFileNum():int
    {
        try {
            $files = $this->scannedMapper->findAll($this->userId);
            return count($files);
        } catch (DoesNotExistException $e) {
            return 0;
        }
    }
}
