<?php
namespace OCA\FilesChecksum\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class ScannedFile extends Entity implements JsonSerializable {

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

    public function __construct() {
      $this->addType('id','integer');
      $this->addType('external', 'boolean');
      $this->addType('finished', 'boolean');
      $this->addType('time','integer');
      $this->addType('scannedNum','integer');
     }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
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

