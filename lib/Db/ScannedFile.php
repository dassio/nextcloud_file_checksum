<?php
namespace OCA\FilesChecksum\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class ScannedFile extends Entity implements JsonSerializable {

    protected $fileId;
		// only filename
    protected $fileName;
		// the full path
    protected $baseName;
		protected $type;
		// boolean value, true means it is an external file
		protected $external;
		protected $userId;
		protected $time;
    protected $finished;
    protected $scannedNum;

    public function jsonSerialize() {
        return [
            'file_id' => $this->fileId,
            'file_name' => $this->fileName,
            'base_name' => $this->baseName,
						'type' => $this->type,
						'external' => $this->external,
						'user_id'  => $this->userId,
						'time' => $this->time,
            'finished' => $this->finished,
            'scanned_num' => $thid->scannedNum
        ];
    }
}

?>

