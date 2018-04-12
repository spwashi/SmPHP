<?php
/**
 * User: Sam Washington
 * Date: 7/19/17
 * Time: 9:33 PM
 */

namespace Sm\Modules\Query\Sql\Constraints;


use Sm\Modules\Query\Sql\Data\Column\ColumnSchema;

class PrimaryKeyConstraintSchema extends StandardKeyConstraintSchema {
    protected $columns;
    public function __construct(ColumnSchema ...$columns) {
        $this->columns = $columns;
    }
}