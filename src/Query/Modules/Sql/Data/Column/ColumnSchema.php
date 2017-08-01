<?php
/**
 * User: Sam Washington
 * Date: 7/18/17
 * Time: 11:58 AM
 */

namespace Sm\Query\Modules\Sql\Data\Column;


use Sm\Core\Schema\Schema;
use Sm\Data\Source\DiscretelySourced;
use Sm\Data\Source\Schema\DataSourceSchema;
use Sm\Data\Source\Database\Table\TableSourceSchema;

/**
 * Class ColumnSchema
 *
 * Meant to represent a Column
 *
 * @package Sm\Query\Modules\Sql\Data\Column
 */
abstract class ColumnSchema implements Schema, DiscretelySourced {
    protected $name;
    /** @var  bool */
    protected $can_be_null = true;
    protected $type;
    protected $unique      = false;
    protected $length;
    /** @var  TableSourceSchema $table_schema */
    protected $table_schema = null;
    
    public function __construct(string $name = null) {
        if ($name) $this->setName($name);
    }
    public static function init() {
        return new static(...func_get_args());
    }
    public function getName(): ?string {
        return $this->name;
    }
    /**
     * @param $name
     *
     * @return \Sm\Query\Modules\Sql\Data\Column\ColumnSchema
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    public function getType():?string {
        return $this->type;
    }
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
    public function getLength():?int {
        return $this->length;
    }
    /**
     * @param int $length
     *
     * @return \Sm\Query\Modules\Sql\Data\Column\ColumnSchema
     */
    public function setLength(int $length) {
        $this->length = $length;
        return $this;
    }
    /**
     * @param bool $nullability
     *
     * @return \Sm\Query\Modules\Sql\Data\Column\ColumnSchema
     */
    public function setNullability($nullability = false) {
        $this->can_be_null = (bool)$nullability;
        return $this;
    }
    public function canBeNull() { return $this->can_be_null; }
    public function isUnique(): bool {
        return $this->unique;
    }
    /**
     * @param bool $unique
     *
     * @return ColumnSchema
     */
    public function setUnique(bool $unique) {
        $this->unique = $unique;
        return $this;
    }
    public function getTableSchema(): ?TableSourceSchema {
        return $this->table_schema;
    }
    public function setTableSchema(TableSourceSchema $table_schema) {
        $this->table_schema = $table_schema;
        return $this;
    }
    /**
     * @return null|\Sm\Data\Source\Schema\DataSourceSchema
     */
    public function getDataSourceSchema():?DataSourceSchema {
        return $this->getTableSchema();
    }
    
}