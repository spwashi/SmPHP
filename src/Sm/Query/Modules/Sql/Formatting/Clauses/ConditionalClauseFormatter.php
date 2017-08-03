<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 6:11 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Clauses;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Query\Modules\Sql\Formatting\Clauses\Exception\IncompleteClauseException;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\Clauses\ConditionalClause;

/**
 * Class WhereClauseFormatter
 *
 * Formats Where clauses
 *
 * @package Sm\Query\Modules\Sql\Formatting\Clause
 */
class ConditionalClauseFormatter extends SqlQueryFormatter {
    /**
     * @param ConditionalClause $conditionalClause
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Query\Modules\Sql\Formatting\Clauses\Exception\IncompleteClauseException
     */
    public function format($conditionalClause): string {
        if (!($conditionalClause instanceof ConditionalClause)) throw new InvalidArgumentException('Can only format WhereClauses');
        $where_clause_str = "";
        $conditions       = $conditionalClause->getConditions();
        if (!count($conditions)) throw new IncompleteClauseException("There are no conditions to the Where clause.");
    
        foreach ($conditions as $index => $condition) {
            if ($index !== 0) $where_clause_str .= ' AND ';
            $where_clause_str .= $this->formatComponent($condition);
        }
        return $where_clause_str;
    }
}