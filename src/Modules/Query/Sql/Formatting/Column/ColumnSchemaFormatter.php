<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:03 PM
 */

namespace Sm\Modules\Query\Sql\Formatting\Column;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Modules\Query\Sql\Data\Column\ColumnSchema;
use Sm\Modules\Query\Sql\Formatting\SqlQueryFormatter;

class ColumnSchemaFormatter extends SqlQueryFormatter {
    public function format($columnSchema): string {
        if (!($columnSchema instanceof ColumnSchema)) {
            throw new InvalidArgumentException("Can only format ColumnSchemas");
        }
        $column_name = $columnSchema->getName();
        $type        = $columnSchema->getType();
        $unique      = $columnSchema->isUnique() ? 'UNIQUE' : '';
        $can_be_null = $columnSchema->canBeNull() ? 'NULL' : 'NOT NULL';
        $length      = $columnSchema->getLength();
        $default     = !is_null($columnSchema->getDefault()) ? $columnSchema->getDefault() : ($columnSchema->canBeNull() ? 'DEFAULT NULL' : '');
        $length      = $length ? "($length)" : '';
        return "{$column_name} {$type}{$length} {$can_be_null} {$unique} {$default}";
    }
    
}