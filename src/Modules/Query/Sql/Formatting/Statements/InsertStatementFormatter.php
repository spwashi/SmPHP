<?php
/**
 * User: Sam Washington
 * Date: 7/13/17
 * Time: 1:08 AM
 */

namespace Sm\Modules\Query\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\Exception\IncompleteFormatterException;
use Sm\Core\Util;
use Sm\Data\Property\Property;
use Sm\Modules\Query\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\Proxy\Source\NamedDataSourceFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\InsertStatement;

class InsertStatementFormatter extends SqlQueryFormatter {
    public function format($item): string {
        if (!($item instanceof InsertStatement)) {
            throw new InvalidArgumentException("Can only format InsertStatements");
        }
        
        list($column_string, $insertExpressionList) = $this->formatInsertExpressionList($item->getInsertedItems());
        
        $sources       = $item->getIntoSources();
        $source_string = $this->formatSourceList($sources);
        
        $update_stmt = "INSERT INTO {$source_string}\n\t\t({$column_string})\nVALUES\t{$insertExpressionList}";
        
        return $update_stmt;
    }
    protected function formatSourceList($source_array): string {
        $sources = [];
        if (!isset($this->formatterManager)) throw new IncompleteFormatterException("No formatter Factory");
        foreach ($source_array as $index => $source) {
            if (count($sources)) throw new UnimplementedError("Inserting into multiple sources");
            $sourceProxy = $this->proxy($source, NamedDataSourceFormattingProxy::class);
            $sources[]   = $this->formatComponent($sourceProxy);
        }
        return join(', ', $sources);
    }
    protected function formatInsertExpressionList(array $inserted_items): array {
        list($column_names, $formatted_columns) = $this->organizeInsertColumns($inserted_items);
        # todo Sets in PHP?
        $insert_array = [];
        foreach ($inserted_items as $index => $inserted_item) {
            $_insert_arr = [];
            foreach ($column_names as $column) {
                if (array_key_exists($column, $inserted_item)) {
                    $raw_value       = $inserted_item[ $column ];
                    $formatted_value = $this->formatComponent($this->formatterManager->placeholder($raw_value, false));
                } else {
                    $formatted_value = 'DEFAULT';
                }
                $_insert_arr[ $column ] = $formatted_value;
            }
            $insert_array[] = '(' . join(', ', $_insert_arr) . ')';
        }
    
        # column string (what we insert into) and the insert array (what we're inserting)
        return [ join(', ', array_unique($formatted_columns)), join(",\n\t\t", $insert_array) ];
    }
    /**
     * @param array $inserted_items
     *
     * @return array
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    private function organizeInsertColumns(array $inserted_items): array {
        $column_names      = [];
        $formatted_columns = [];
        foreach ($inserted_items as $number => $insertBatch) {
            if ($insertBatch instanceof Property) {
                $insertBatch = [
                    $insertBatch->getName() => $insertBatch->value,
                ];
            } else if (!is_array($insertBatch)) {
                throw new InvalidArgumentException("Can only insert using arrays (attempting ' " . Util::getShape($insertBatch) . "')");
            }
            foreach ($insertBatch as $column_name => $value) {
                if (is_numeric($column_name)) {
                    throw new InvalidArgumentException("The insert array should only be associative - (index {$column_name} in entry {$number} is a number)");
                }
                $column_names[ $column_name ] = null;
                
                # Assume it's a column - otherwise, we'd use a different object
                $formatted_columns[] = $this->formatComponent($this->proxy($column_name,
                                                                           ColumnIdentifierFormattingProxy::class));
            }
        }
        $column_names = array_keys($column_names);
        return [ $column_names, $formatted_columns ];
    }
}