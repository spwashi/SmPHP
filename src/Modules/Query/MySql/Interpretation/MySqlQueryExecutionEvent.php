<?php


namespace Sm\Modules\Query\MySql\Interpretation;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Modules\Query\Sql\Event\SqlQueryExecutionEvent;

class MySqlQueryExecutionEvent extends SqlQueryExecutionEvent {
    protected $formatted_query_with_inline_variables;
    
    public function getStatementHandler(): \PDOStatement {
        return parent::getStatementHandler();
    }
    /**
     * @param \PDOStatement $statementHandler
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function setStatementHandler($statementHandler) {
        if (!($statementHandler instanceof \PDOStatement)) {
            throw new InvalidArgumentException("Can only use PDOStatements for this Handler");
        }
        parent::setStatementHandler($statementHandler);
        return $this;
    }
    public function setFormattedQueryWithInlineVariables($formatted_query_with_inline_variables) {
        $this->formatted_query_with_inline_variables = $formatted_query_with_inline_variables;
        return $this;
    }
    public function jsonSerialize() {
        return array_merge(parent::jsonSerialize(),
                           [
                               'formatted_query_with_variables' => $this->formatted_query_with_inline_variables,
                           ]);
    }
}