<?php
// db/FileCacheMapper.php

namespace OCA\FilesChecksum\Db;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;
use OCP\AppFramework\Db\DoesNotExistException;

class ScannedFileMapper extends QBMapper {

  private $table_name = 'fileschecksum';

  public function __construct(IDBConnection $db) {
    parent::__construct($db, $this->table_name,ScannedFile::class);
  }

  public function find(int $id,string $userId){
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
       ->from($this->table_name)
       ->where(
         $qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
       )->andWhere(
         $qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_INT))
       );
    try{
      return $this->findEntity($qb);
    }catch(DoesNotExistException $e){
      return null;
    }

  }
  /**
   * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
   *
   *  @param string $id
   */
  public function findAll(string $userId) {
    $qb = $this->db->getQueryBuilder();

    $qb->select('*')
       ->from($this->table_name)
       ->where(
         $qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_INT))
       );

    return $this->findEntities($qb);
  }
}
