<?php


namespace Sm\Modules\Query\MySql\Interpretation;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Modules\Query\Sql\Event\SqlQueryExecutionEvent;

class MySqlQueryExecutionEvent extends SqlQueryExecutionEvent {
    protected $formatted_query_with_inline_variables;
    
    public function getStatementHandle(): \PDOStatement {
        return parent::getStatementHandle();
    }
    /**
     * @param \PDOStatement $statementHandler
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function setStatementHandle($statementHandler) {
        if (!($statementHandler instanceof \PDOStatement)) {
            throw new InvalidArgumentException("Can only use PDOStatements for this Handler");
        }
        parent::setStatementHandle($statementHandler);
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