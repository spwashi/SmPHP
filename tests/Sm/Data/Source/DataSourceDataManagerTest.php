<?php

namespace Sm\Data\Source;


use Sm\Data\Source\Database\Table\TableSourceSchema;
use Sm\Data\Source\Schema\DataSourceSchema;

class DataSourceDataManagerTest extends \PHPUnit_Framework_TestCase {
    public function testCanConfigureTable() {
        $table_name  = 'tbl';
        $table       = [ 'name' => $table_name, 'type' => 'table' ];
        $tableSchema = DataSourceDataManager::init()->configure($table);
        $this->assertInstanceOf(TableSourceSchema::class, $tableSchema);
    }
    public function testCanConfigureDatabase() {
        $db_name          = 'database';
        $db_config        = [ 'name' => $db_name, 'type' => 'database' ];
        $dataSourceSchema = DataSourceDataManager::init()->configure($db_config);
        $this->assertInstanceOf(DataSourceSchema::class, $dataSourceSchema);
    }
}
