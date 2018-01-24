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
    
        $default_val = strtolower($item->getDefault() ?? '');
        $update_val  = strtolower($item->getOnUpdate() ?? '');
    
        $default   = $default_val === 'now' || $default_val === 'current_timestamp' ? 'DEFAULT CURRENT_TIMESTAMP' : '';
        $on_update = $update_val === 'now' || $update_val === 'current_timestamp' ? 'ON UPDATE CURRENT_TIMESTAMP' : '';
    
        return "{$column_name} {$type} {$can_be_null} {$default} {$on_update}";
    }
}