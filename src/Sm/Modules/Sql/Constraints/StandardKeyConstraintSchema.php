<?php
/**
 * User: Sam Washington
 * Date: 7/26/17
 * Time: 6:45 PM
 */

namespace Sm\Modules\Sql\Constraints;


use Sm\Modules\Sql\Data\Column\ColumnSchema;

/**
 * Class StandardKeyConstraintSchema
 *
 *
 * Represents KeyConstraints
 *
 * @package Sm\Modules\Sql\Constraints
 */
abstract class StandardKeyConstraintSchema implements KeyConstraintSchema, \JsonSerializable {
    /** @var \Sm\Modules\Sql\Data\Column\ColumnSchema[] */
    protected $columns;
    public static function init() {
        return new static(...func_get_args());
    }
    /**
     * @return \Sm\Modules\Sql\Data\Column\ColumnSchema[]
     */
    public function getColumns(): array {
        return $this->columns;
    }
    public function addColumn(ColumnSchema  ...$columnSchema) {
        $this->columns = array_merge($this->columns, $columnSchema);
        return $this;
    }
    function jsonSerialize() {
        return get_object_vars($this);
    }
}