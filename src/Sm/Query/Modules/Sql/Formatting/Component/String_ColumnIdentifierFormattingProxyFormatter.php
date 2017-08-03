<?php
/**
 * User: Sam Washington
 * Date: 7/25/17
 * Time: 9:37 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Component;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\String_ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\Table\TableIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;

class String_ColumnIdentifierFormattingProxyFormatter extends SqlQueryFormatter {
    /**
     * Format the String_ColumnIdentifierFormattingProxy
     *
     * @param \Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy $columnFormattingProxy
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function format($columnFormattingProxy): string {
        if (!($columnFormattingProxy instanceof String_ColumnIdentifierFormattingProxy)) {
            throw new InvalidArgumentException("Can only format String_ColumnIdentifierFormattingProxies");
        }
    
        $column_name = $columnFormattingProxy->getColumnName();
        $table       = $columnFormattingProxy->getSource();
        if ($columnFormattingProxy->doFormatAsString()) {
            return "\"$column_name\"";
        }
        if ($table) {
            $aliasedTableProxy     = $this->getFinalAlias($table);
            $aliasAsTableReference = $this->proxy($aliasedTableProxy, TableIdentifierFormattingProxy::class);
            $column_name           = $this->formatComponent($aliasAsTableReference) . '.' . "`$column_name`";
        }
        return $column_name;
    }
}