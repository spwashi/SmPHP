<?php
/**
 * User: Sam Washington
 * Date: 7/7/17
 * Time: 8:17 AM
 */

namespace Sm\Query\Statements\Clauses;

/**
 * Class ConditionalClause
 *
 * Represents a string of Conditions
 *
 * @package Sm\Query\Statements\Clauses
 */
class ConditionalClause {
    protected $conditions = [];
    public function __construct(...$conditions) {
        $this->conditions = $conditions;
    }
    /**
     * Returns TRUE if there are no conditions in the WHERE clause
     *
     * @return bool
     */
    public function isEmpty(): bool {
        return !count($this->conditions);
    }
    /**
     * Get the Conditions of the WhereClause
     *
     * @return array
     */
    public function getConditions(): array {
        return $this->conditions;
    }
}