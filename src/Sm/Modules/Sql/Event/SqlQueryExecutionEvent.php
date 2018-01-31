<?php


namespace Sm\Modules\Sql\Event;


use Sm\Core\Event\Event;

class SqlQueryExecutionEvent extends Event {
    protected $query;
    protected $query_variables;
    protected $formatted_query;
    protected $executionSuccess;
    private   $statementHandler;
    public function __construct($query = null,
    
                                $statementHandler = null,
    
                                $formatted_query = null,
    
                                $result = null,
                                $variables = null) {
        parent::__construct();
        $this->query            = $query;
        $this->query_variables  = $variables;
        $this->formatted_query  = $formatted_query;
        $this->executionSuccess = $result;
        $this->statementHandler = $statementHandler;
    }
    public static function init($query = null,
    
                                $statementHandler = null,
    
                                $formatted_query = null,
    
                                $result = null,
                                $variables = null) {
        return new static (...func_get_args());
    }
    public function getQuery() {
        return $this->query;
    }
    public function setQuery($query) {
        $this->query = $query;
        return $this;
    }
    public function getQueryVariables() {
        return $this->query_variables;
    }
    public function setQueryVariables($query_variables) {
        $this->query_variables = $query_variables;
        return $this;
    }
    public function getFormattedQuery() {
        return $this->formatted_query;
    }
    public function setFormattedQuery($formatted_query) {
        $this->formatted_query = $formatted_query;
        return $this;
    }
    public function getExecutionSuccess() {
        return $this->executionSuccess;
    }
    public function setExecutionSuccess($executionSuccess) {
        $this->executionSuccess = $executionSuccess;
        return $this;
    }
    public function getStatementHandler() {
        return $this->statementHandler;
    }
    public function setStatementHandler($statementHandler) {
        $this->statementHandler = $statementHandler;
        return $this;
    }
    public function jsonSerialize() {
        return array_merge(parent::jsonSerialize(),
                           [
                               'query'           => $this->query,
                               'result'          => $this->executionSuccess,
                               'formatted_query' => $this->formatted_query,
                               'query_variables' => $this->query_variables,
                           ]);
    }
}