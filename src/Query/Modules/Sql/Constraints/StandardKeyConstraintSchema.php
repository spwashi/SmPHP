<?php
/**
 * User: Sam Washington
 * Date: 7/26/17
 * Time: 6:45 PM
 */

namespace Sm\Query\Modules\Sql\Constraints;


use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;

/**
 * Class StandardKeyConstraintSchema
 *
 *
 * Represents KeyConstraints
 *
 * @package Sm\Query\Modules\Sql\Constraints
 */
abstract class StandardKeyConstraintSchema implements KeyConstraintSchema {
    /** @var \Sm\Query\Modules\Sql\Data\Column\ColumnSchema[] */
    protected $columns;
    public static function init() {
        return new static(...func_get_args());
    }
    /**
     * @return \Sm\Query\Modules\Sql\Data\Column\ColumnSchema[]
     */
    public function getColumns(): array {
        return $this->columns;
    }
    public function addColumn(ColumnSchema $columnSchema) {
        $this->columns[] = $columnSchema;
        return $this;
    }
}