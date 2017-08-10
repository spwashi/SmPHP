<?php
/**
 * User: Sam Washington
 * Date: 7/19/17
 * Time: 9:33 PM
 */

namespace Sm\Query\Modules\Sql\Constraints;


use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;

class PrimaryKeyConstraintSchema extends StandardKeyConstraintSchema implements \JsonSerializable {
    protected $columns;
    public function __construct(ColumnSchema ...$columns) {
        $this->columns = $columns;
    }
    function jsonSerialize() {
        return get_object_vars($this);
    }
}