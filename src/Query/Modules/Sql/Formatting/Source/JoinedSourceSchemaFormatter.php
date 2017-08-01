<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 9:58 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Source;


use Sm\Core\Exception\Error;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Util;
use Sm\Data\Source\Constructs\JoinedSourceSchema;
use Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing\AliasedSourceFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Component\SelectExpressionFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\Table\TableIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\Clauses\ConditionalClause;

class JoinedSourceSchemaFormatter extends SqlQueryFormatter {
    public function prime($joinedSourceSchema) {
        if (!($joinedSourceSchema instanceof JoinedSourceSchema)) throw new InvalidArgumentException("Can only format JoinedSourceSchemas. [" . Util::getShapeOfItem($joinedSourceSchema) . '] given');
        $originalSource = $joinedSourceSchema->getOriginSources()[0];
        $this->createSelectExpression($originalSource);
    }
    public function format($joinedSourceSchema): string {
        if (!($joinedSourceSchema instanceof JoinedSourceSchema)) throw new InvalidArgumentException("Can only format JoinedSourceSchemas. [" . Util::getShapeOfItem($joinedSourceSchema) . '] given');
        $originalSources = $joinedSourceSchema->getOriginSources();
        $joinedSources   = $joinedSourceSchema->getJoinedSources();
        if (count($originalSources) !== 1) throw new Error("Can only format JOINS on one source");
        if (count($joinedSources) !== 1) throw new Error("Can only format JOINS with one Source");
        
        # Format the Original Source so we can Alias it in the SelectExpression
        $originalSource           = $this->createSelectExpression($originalSources[0]);
        $formattedOriginalSources = $this->formatComponent($originalSource);
        $joinedSource             = $this->createSelectExpression($joinedSources[0]);
        $formattedJoinedSources   = $this->formatComponent($joinedSource);
        
        # Format the "ON" condition
        $joinConditions           = $joinedSourceSchema->getJoinConditions();
        $join_condition_statement = '';
        if (!empty($joinConditions)) $join_condition_statement = 'ON ' . $this->formatComponent(new ConditionalClause(...$joinConditions));
        
        return "{$formattedOriginalSources} LEFT JOIN {$formattedJoinedSources} {$join_condition_statement}";
    }
    protected function createSelectExpression($source) {
        $alias = $this->alias($source, AliasedSourceFormattingProxy::class);
        return $this->proxy([ $source, $alias ], SelectExpressionFormattingProxy::class);
    }
    protected function formatSources($sources) {
        $formattedSources = [];
        foreach ($sources as $source) {
            $proxy              = $this->proxy($source, TableIdentifierFormattingProxy::class);
            $formattedSources[] = $this->formatComponent($proxy);
        }
        return $formattedSources;
    }
}