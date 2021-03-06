<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:49 PM
 */

namespace Sm\Data\Source\Database\Table;


use Sm\Data\Property\PropertySchemaContainer;
use Sm\Data\Source\Database\DatabaseSource;
use Sm\Data\Source\DataSource;

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */

/**
 * Class TableSource
 *
 * Represents a Source from a Table
 *
 * @property-read \Sm\Data\Source\Database\ColumnSchemaContainer $columns
 *
 * @package Sm\Data\Source\Database
 */
class TableSource extends DataSource implements TableSourceSchema, \JsonSerializable {
    protected $table_name;
    /** @var  DatabaseSource $databaseSource */
    protected $databaseSource;
    /** @var PropertySchemaContainer $columnContainer */
    protected $columnContainer;
    
    public function __construct($table_name = null, DatabaseSource $databaseSource = null) {
        parent::__construct();
        $this->table_name     = $table_name;
        $this->databaseSource = $databaseSource;
    }
    public static function init($table_name = null, DatabaseSource $databaseSource = null) {
        return new static($table_name, $databaseSource);
    }
    public function __get($name) {
        if ($name === 'columns') return $this->columnContainer;
        return null;
    }
    public function getParentSource(): ?DatabaseSource {
        return $this->databaseSource;
    }
    /**
     * Get the name of the table
     *
     * @return string
     */
    public function getName(): ?string {
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
    public function jsonSerialize() {
        $json = [ 'name' => $this->table_name, ];
        
        if (isset($this->databaseSource)) $json['database'] = $this->databaseSource;
        
        return $json;
    }
}