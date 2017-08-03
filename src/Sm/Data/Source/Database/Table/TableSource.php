<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:49 PM
 */

namespace Sm\Data\Source\Database\Table;


use Sm\Authentication\Authentication;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Source\Database\DatabaseDataSource;
use Sm\Data\Source\DataSource;

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */

/**
 * Class TableSource
 *
 * Represents a Source from a Table
 *
 * @property-read \Sm\Data\Source\Database\ColumnContainer $columns
 *
 * @method static TableSource init(DatabaseDataSource $DatabaseSource, string $table_name = null)
 * @package Sm\Data\Source\Database
 */
class TableSource extends DataSource implements TableSourceSchema {
    protected $table_name;
    /** @var  DatabaseDataSource $databaseSource */
    protected $databaseSource;
    /** @var PropertyContainer $columnContainer */
    protected $columnContainer;
    
    public function __construct($table_name, DatabaseDataSource $DatabaseSource = null) {
        parent::__construct();
        $this->table_name     = $table_name;
        $this->databaseSource = $DatabaseSource;
    }
    public function __get($name) {
        if ($name === 'columns') return $this->columnContainer;
        return null;
    }
    public function getParentSource(): ?DatabaseDataSource {
        return $this->databaseSource;
    }
    /**
     * Get the name of the table
     *
     * @return string
     */
    public function getName():?string {
        return $this->table_name;
    }
    /**
     * @param mixed $table_name
     *
     * @return TableSource
     */
    public function setTableName($table_name) {
        $this->table_name = $table_name;
        return $this;
    }
    public function authenticate(Authentication $authentication = null) {
        $this->databaseSource->authenticate($authentication);
        return $this;
    }
}