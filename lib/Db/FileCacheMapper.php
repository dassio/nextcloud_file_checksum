<?php
// db/FileCacheMapper.php

namespace OCA\MyApp\Db;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class FileCacheMapper extends QBMapper {

    private $table_name = 'oca_filechecksum_jobs';

    public function __construct(IDBConnection $db) {
        parent::__construct($db, $this->table_name);
    }


    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     * 
     *  @param string $id
     */
    public function find(string $file_name) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('fileid,path,etag,chedk,checksum')
           ->from($this->table_name)
           ->where(
               $qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_string))
           );

        return $this->findEntity($qb);
    }
}