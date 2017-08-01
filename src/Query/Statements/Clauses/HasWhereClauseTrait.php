<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 11:07 PM
 */

namespace Sm\Query\Statements\Clauses;

use Sm\Data\Evaluation\EvaluableStatement;

/**
 * Trait HasWhereClauseTrait
 *
 * Represents Statements that have WhereClauses
 *
 * @package Sm\Query\Statements
 */
trait HasWhereClauseTrait {
    /** @var array The Conditions that are going to be ANDed together as part of the "WHERE" clause */
    protected $conditions = [];
    /**
     * Set the Conditions that are going to be a part of the "WHERE" clause
     *
     * @param EvaluableStatement[]|array ...$conditions
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function where(EvaluableStatement ...$conditions) {
        $this->conditions = array_merge($this->conditions, $conditions);
        return $this;
    }
    /**
     * Get the WhereClause used in this Statement
     *
     * @return null|\Sm\Query\Statements\Clauses\ConditionalClause
     */
    public function getWhereClause():?ConditionalClause {
        return count($this->conditions)
            ? new ConditionalClause(...$this->conditions)
            : null;
    }
}