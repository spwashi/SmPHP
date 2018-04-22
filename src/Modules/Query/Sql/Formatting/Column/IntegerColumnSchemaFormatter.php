<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:13 PM
 */

namespace Sm\Modules\Query\Sql\Formatting\Column;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Modules\Query\Sql\Data\Column\IntegerColumnSchema;

class IntegerColumnSchemaFormatter extends ColumnSchemaFormatter {
    public function format($columnSchema): string {
        if (!($columnSchema instanceof IntegerColumnSchema)) {
            throw new InvalidArgumentException("Can only format IntegerColumnSchemas");
        }
        $column_name    = $columnSchema->getName();
        $type           = $columnSchema->getType();
        $can_be_null    = $columnSchema->canBeNull() ? 'NULL' : 'NOT NULL';
        $unique         = $columnSchema->isUnique() ? 'UNIQUE' : '';
        $length         = $columnSchema->getLength();
        $default_val    = $columnSchema->getDefault();
        $default        = !is_null($default_val) ? "DEFAULT {$default_val}" : ($columnSchema->canBeNull() ? 'DEFAULT NULL' : '');
        $auto_increment = $columnSchema->isAutoIncrement() ? 'AUTO_INCREMENT' : '';
        $length         = $length ? "($length)" : '';
        return "{$column_name} {$type}{$length} {$can_be_null} {$auto_increment} {$unique} {$default}";
    }
}