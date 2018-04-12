<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 9:45 PM
 */

namespace Sm\Modules\Query\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Data\Source\Constructs\JoinedSourceSchema;
use Sm\Data\Source\Database\Table\TableSourceSchema;
use Sm\Modules\Query\Sql\Formatting\Proxy\Aliasing\AliasedSourceFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\Proxy\Component\SelectExpressionFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\Proxy\Source\Table\TableIdentifierFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\SqlQueryFormatter;
use Sm\Modules\Query\Sql\Formatting\Statements\Exception\MalformedStatementException;
use Sm\Query\Statements\SelectStatement;

class SelectStatementFormatter extends SqlQueryFormatter implements Formatter {
    /**
     * @param $item
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function prime($item) {
        if (!($item instanceof SelectStatement)) throw new InvalidArgumentException("Can only format SelectStatements");
        $sources = $item->getFromSources();
        $this->getPrimedSources($sources);
    }
    /**
     * Return the item Formatted in the specific way
     *
     * @param SelectStatement $item
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Modules\Query\Sql\Formatting\Statements\Exception\MalformedStatementException
     */
    public function format($item): string {
        if (!($item instanceof SelectStatement)) {
            throw new InvalidArgumentException("Can only format SelectStatements");
        }
        $this->prime($item);
        $sources     = $this->getPrimedSources($item->getFromSources());
        $whereClause = $item->getWhereClause();
    
        $selectedItems          = $item->getSelectedItems();
        $select_expression_list = $this->formatSelectExpressionList($selectedItems);
        $from_string            = count($sources) ? "FROM\t" . $this->formatSelectFromList($sources) : '';
        $where_string           = $whereClause ? "WHERE\t" . $this->formatComponent($whereClause) : '';
        $select_stmt_string     = "SELECT\t{$select_expression_list}\n{$from_string}\n{$where_string}";
    
        return trim($select_stmt_string);
    }
    /**
     * Make sure the "source" is sturctured as something we'd use.
     *
     * @param $source
     *
     * @return mixed|null|\Sm\Data\Source\Constructs\JoinedSourceSchematic|\Sm\Data\Source\Database\Table\TableSourceSchema
     */
    protected function convertToUsableSource($source) {
        # The important thing is to get it in the structure of
        if ($source instanceof TableSourceSchema) {
            $tableProxy = $source;
        } else if ($source instanceof JoinedSourceSchema) {
            $tableProxy = $source;
        } else {
            $tableProxy = $this->proxy($source, TableIdentifierFormattingProxy::class);
        }
        return $tableProxy;
    }
    /**
     * Prepare the Sources to be used in the Query (getting all Aliases set up mostly)
     *
     * @param array $sources
     *
     * @return array
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function getPrimedSources(array $sources) {
        $top_level_joins = [];
        foreach ($sources as $source) {
            # Don't alias strings
            if (is_string($source)) continue;
    
            # structure the table as such
            $source = $this->convertToUsableSource($source);
            
            if ($source instanceof JoinedSourceSchema) {
                $this->primeComponent($source);
                $top_level_joins = array_merge($source->getOriginSources());
            } else {
                # alias the source
                $this->alias($source, AliasedSourceFormattingProxy::class);
            }
        }
        
        # Remove all of the tables that are exactly covered by the JOINs
        foreach ($top_level_joins as $table) {
            $index = array_search($table, $sources);
            # If something from the original sources
            if ($index !== false) unset($sources[ $index ]);
        }
        
        return $sources;
    }
    /**
     * Format the things that will be in the "select list"
     *
     * @param $source_array
     *
     * @return string
     * @throws \Sm\Modules\Query\Sql\Formatting\Statements\Exception\MalformedStatementException
     */
    protected function formatSelectFromList($source_array): string {
        $sources = [];
        foreach ($source_array as $index => $source) {
            $sourceProxy      = $this->convertToUsableSource($source);
            $selectExpression = $this->proxy($sourceProxy, SelectExpressionFormattingProxy::class);
            $formatted_source = $this->formatComponent($selectExpression);
            $sources[]        = $formatted_source;
        }
        if (empty($sources)) {
            throw new MalformedStatementException("Cannot select without any sources.");
        }
        return join(",\n\t\t", $sources);
    }
    /**
     * Format the select expression based on the selected items
     *
     * @param array $selects
     *
     * @return string
     * @throws \Sm\Modules\Query\Sql\Formatting\Statements\Exception\MalformedStatementException
     */
    protected function formatSelectExpressionList(array $selects): string {
        $expression_list = [];
        foreach ($selects as $item) {
            # Assume it's a column - otherwise, we'd use a different object
            $proxy             = $this->proxy($item, ColumnIdentifierFormattingProxy::class);
            $expression_list[] = $this->formatComponent($proxy);
        }
        if (empty($expression_list)) {
            throw new MalformedStatementException("Cannot select without any items being selected.");
        }
        return join(",\n\t\t", $expression_list);
    }
}