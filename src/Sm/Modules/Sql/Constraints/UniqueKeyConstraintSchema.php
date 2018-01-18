<?php
/**
 * User: Sam Washington
 * Date: 7/19/17
 * Time: 9:33 PM
 */

namespace Sm\Modules\Sql\Constraints;


use Sm\Modules\Sql\Data\Column\ColumnSchema;

class UniqueKeyConstraintSchema extends StandardKeyConstraintSchema {
    protected $columns;
    public function __construct(ColumnSchema ...$columns) {
        $this->columns = $columns;
    }
}