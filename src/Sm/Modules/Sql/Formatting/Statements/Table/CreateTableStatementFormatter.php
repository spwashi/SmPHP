<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 7:30 PM
 */

namespace Sm\Modules\Sql\Formatting\Statements\Table;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Modules\Sql\Constraints\KeyConstraintSchema;
use Sm\Modules\Sql\Data\Column\ColumnSchema;
use Sm\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Modules\Sql\Statements\CreateTableStatement;

/**
 * Class CreateTableStatementFormatter
 *
 * Meant to format statements to create a table
 *
 * @package Sm\Modules\Sql\Formatting\Statements\Table
 */
class CreateTableStatementFormatter extends SqlQueryFormatter {
    /**
     * @param $item
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function format($item): string {
        if (!($item instanceof CreateTableStatement)) throw new UnimplementedError("+ Anything but CreateTableStatements");
        $table_name                     = $item->getName();
        $columns                        = $item->getColumns();
        $constraints                    = $item->getConstraints();
        $indexed                        = $item->getIndexedColumns();
        $formattedColumnsAndConstraints = [];
        foreach ($columns as $column) {
            if (!($column instanceof ColumnSchema)) throw new InvalidArgumentException("Can only create tables with column schemas");
            $formattedColumnsAndConstraints[] = $this->formatComponent($column);
        }
    
        foreach ($indexed as $indexed_column) {
            if (!($indexed_column instanceof ColumnSchema)) throw new InvalidArgumentException("Can only create tables with column schemas");
            $length                           = $indexed_column->getLength();
            $name                             = $indexed_column->getName();
            $formattedColumnsAndConstraints[] = "INDEX({$name}({$length}))";
        }
        
        foreach ($constraints as $constraint) {
            if (!($constraint instanceof KeyConstraintSchema)) throw new InvalidArgumentException("Can only create tables with KeyConstraints");
            $formattedColumnsAndConstraints[] = $this->formatComponent($constraint);
        }
    
    
        $f_c_string = join(",\n\t", $formattedColumnsAndConstraints);
        return "CREATE TABLE IF NOT EXISTS {$table_name} (\n\t{$f_c_string}\n)";
    }
    
}