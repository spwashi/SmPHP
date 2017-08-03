<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:13 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Column;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Query\Modules\Sql\Data\Column\IntegerColumnSchema;

class IntegerColumnSchemaFormatter extends ColumnSchemaFormatter {
    public function format($item): string {
        if (!($item instanceof IntegerColumnSchema)) {
            throw new InvalidArgumentException("Can only format IntegerColumnSchemas");
        }
        $column_name    = $item->getName();
        $type           = $item->getType();
        $can_be_null    = $item->canBeNull() ? 'NULL' : 'NOT NULL';
        $unique         = $item->isUnique() ? 'UNIQUE' : '';
        $length         = $item->getLength();
        $auto_increment = $item->isAutoIncrement() ? 'AUTO INCREMENT' : '';
        $length         = $length ? "($length)" : '';
        return "{$column_name} {$type}{$length} {$can_be_null} {$auto_increment} {$unique}";
    }
}