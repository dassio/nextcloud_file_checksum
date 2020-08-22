<?php
namespace OCA\FilesChecksum\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20200822183800 extends SimpleMigrationStep
{

  /**
    * @param IOutput $output
    * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
    * @param array $options
    * @return null|ISchemaWrapper
   */
  public function changeSchema(IOutput $output, Closure $schemaClosure, array $options)
  {
    /** @var ISchemaWrapper $schema */
    $schema = $schemaClosure();
    $tableName = "fileschecksum";
    if (!$schema->hasTable($tableName)) {
      $table = $schema->createTable($tableName);
      $table->addColumn('id', 'integer', [
        'notnull' => true,
      ]);
      $table->addColumn('file_name', 'string', [
        'notnull' => true,
        'length' => 200
      ]);
      $table->addColumn('base_name', 'string', [
        'notnull' => true,
        'length' => 200,
      ]);
      $table->addColumn('type', 'string', [
        'notnull' => true,
        'default' => 'file'
      ]);
      $table->addColumn('external', 'boolean', [
        'notnull' => true
      ]);
      $table->addColumn('user_id', 'string', [
        'notnull' => true
      ]);
      $table->addColumn('time', 'integer', [
        'notnull' => true
      ]);
      $table->addColumn('finished', 'boolean', [
        'notnull' => false
      ]);
      $table->addColumn('scanned_num', 'integer', [
        'notnull' => false
      ]);
      $table->addColumn('has_checksum', 'boolean', [
        'notnull' => true
      ]);
      $table->addColumn('metadata', 'text', [
        'notnull' => false
      ]);


      $table->setPrimaryKey(['id']);
    }
    return $schema;
  }
}
