<?php


namespace Sm\Modules\Sql\Formatting\Column;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Modules\Sql\Data\Column\DateTimeColumnSchema;

class DateTimeColumnSchemaFormatter extends ColumnSchemaFormatter {
    public function format($item): string {
        if (!($item instanceof DateTimeColumnSchema)) {
            throw new InvalidArgumentException("Can only format DateTimeColumnSchemas");
        }
        $column_name = $item->getName();
        $type        = $item->getType();
        $can_be_null = $item->canBeNull() ? 'NULL' : 'NOT NULL';
        
        $default       = $item->getDefault() !== null ? 'DEFAULT ' . $item->getDefault() : '';
        $get_on_update = $item->getOnUpdate() !== null ? 'ON UPDATE ' . $item->getOnUpdate() : '';
        
        return "{$column_name} {$type} {$can_be_null} {$default} {$get_on_update}";
    }
}