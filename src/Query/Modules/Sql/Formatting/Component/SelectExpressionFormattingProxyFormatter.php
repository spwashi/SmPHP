<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 9:14 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Component;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Formatting\FormattingProxy;
use Sm\Data\Source\Constructs\JoinedSourceSchema;
use Sm\Query\Modules\Sql\Formatting\Proxy\Component\SelectExpressionFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\Table\TableIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;

class SelectExpressionFormattingProxyFormatter extends SqlQueryFormatter {
    /**
     * Format the String_ColumnIdentifierFormattingProxy
     *
     * @param \Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy $formattingProxy
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function format($formattingProxy): string {
        if (!($formattingProxy instanceof SelectExpressionFormattingProxy)) {
            throw new InvalidArgumentException("Can only format SelectExpressionFormattingProxies");
        }
        $alias  = $formattingProxy->getAlias();
        $source = $formattingProxy->getSource();
        
        if (($source instanceof FormattingProxy) || ($source instanceof JoinedSourceSchema)) {
            $sourceProxy = $source;
        } else {
            $sourceProxy = $this->proxy($source, TableIdentifierFormattingProxy::class);
        }
        
        $formattedSource = $this->formatComponent($sourceProxy);
        if ($alias) {
            
            if ($alias instanceof FormattingProxy) $aliasProxy = $alias;
            else $aliasProxy = $this->proxy($alias, TableIdentifierFormattingProxy::class);
            
            return "{$formattedSource} AS " . $this->formatComponent($aliasProxy);
        } else {
            return $formattedSource;
        }
    }
    
}