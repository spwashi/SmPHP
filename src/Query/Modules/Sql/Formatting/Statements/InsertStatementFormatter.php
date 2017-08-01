<?php
/**
 * User: Sam Washington
 * Date: 7/13/17
 * Time: 1:08 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\Exception\IncompleteFormatterException;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\NamedDataSourceFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\InsertStatement;

class InsertStatementFormatter extends SqlQueryFormatter {
    public function format($item): string {
        if (!($item instanceof InsertStatement)) throw new InvalidArgumentException("Can only format InsertStatements");
        
        list($column_string, $insertExpressionList) = $this->formatInsertExpressionList($item->getInsertedItems());
        
        $sources       = $item->getIntoSources();
        $source_string = $this->formatSourceList($sources);
        
        $update_stmt = "INSERT INTO {$source_string}\n\t\t({$column_string})\nVALUES\t{$insertExpressionList}";
        
        return $update_stmt;
    }
    protected function formatSourceList($source_array): string {
        $sources = [];
        if (!isset($this->queryFormatter)) throw new IncompleteFormatterException("No formatter Factory");
        foreach ($source_array as $index => $source) {
            if (count($sources)) throw new UnimplementedError("Inserting into multiple sources");
            $sourceProxy = $this->proxy($source, NamedDataSourceFormattingProxy::class);
            $sources[]   = $this->formatComponent($sourceProxy);
        }
        return join(', ', $sources);
    }
    protected function formatInsertExpressionList(array $inserted_items): array {
        $columns           = [];
        $formatted_columns = [];
        foreach ($inserted_items as $number => $insert_collection) {
            if (!is_array($insert_collection)) throw new InvalidArgumentException("Trying to insert a non-array (index {$number})");
            foreach ($insert_collection as $column_name => $value) {
                if (is_numeric($column_name)) throw new InvalidArgumentException("Trying to insert a non-associative array (index {$column_name} in {$number})");
                $columns[ $column_name ] = null;
                # Assume it's a column - otherwise, we'd use a different object
                $formatted_columns[] = $this->formatComponent($this->proxy($column_name,
                                                                           ColumnIdentifierFormattingProxy::class));
            }
        }
        # todo Sets in PHP?
        $columns      = array_keys($columns);
        $insert_array = [];
        foreach ($inserted_items as $index => $inserted_item) {
            $_insert_arr = [];
            foreach ($columns as $column) {
                if (array_key_exists($column, $inserted_item)) {
                    $_insert_arr[ $column ] = $this->formatComponent($inserted_item[ $column ]);
                } else {
                    $_insert_arr[ $column ] = 'DEFAULT';
                }
            }
            $insert_array[] = '(' . join(', ', $_insert_arr) . ')';
        }
    
        # column string (what we insert into) and the insert array (what we're inserting)
        return [ join(', ', array_unique($formatted_columns)), join(",\n\t\t", $insert_array) ];
    }
}