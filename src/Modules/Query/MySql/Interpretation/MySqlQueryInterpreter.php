<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 5:21 PM
 */

namespace Sm\Modules\Query\MySql\Interpretation;


use Sm\Authentication\Authentication;
use Sm\Authentication\Exception\InvalidAuthenticationException;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\TypeMismatchException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Internal\Monitor\Monitor;
use Sm\Core\Util;
use Sm\Modules\Query\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Query\Sql\Event\SqlQueryExecutionEvent;
use Sm\Modules\Query\Sql\SqlDisplayContext;
use Sm\Modules\Query\Sql\SqlExecutionContext;
use Sm\Modules\Query\Sql\SqlQueryInterpreter;


/**
 * Class MySqlQueryInterpreter
 *
 * Query interpreter for statements that refer to items stored in a MySql database
 *
 * @package Sm\Modules\Query\MySql
 */
class MySqlQueryInterpreter extends SqlQueryInterpreter {
    use HasMonitorTrait;
    const MONITOR__QUERY_EXECUTED = 'QUERY__EXECUTED';
    protected $logQueries = true;
    
    /** @var  MySqlAuthentication $authentication */
    protected $authentication;
    /** @var  \PDO */
    protected $connection;
    /**
     * Check to see if an Authentication can be used to interpret these Queries.
     * Throw an error on failure
     *
     * @param MySqlAuthentication|Authentication $authentication
     *
     * @return mixed|void
     * @throws \Sm\Authentication\Exception\InvalidAuthenticationException
     * @throws \Sm\Core\Exception\TypeMismatchException
     */
    public function checkAuthenticationValidity(Authentication $authentication) {
        # This should actually probably be a part of the execution context...
        if (!($authentication instanceof MySqlAuthentication)) throw new TypeMismatchException("Can only connect with a MySqlAuthentication");
        $authentication->connect();
        if (!$authentication->isValid()) throw new InvalidAuthenticationException("The Authentication for this");
    }
    
    /**
     * @param $query_or_statement
     *
     * @return \Sm\Modules\Query\Sql\Event\SqlQueryExecutionEvent
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function execute($query_or_statement): SqlQueryExecutionEvent {
        $formattingContext = new SqlExecutionContext;
        $formatted_query   = $this->format($query_or_statement, $formattingContext);
        $bound_variables   = $this->getBoundVariables($formattingContext);
        $connection        = $this->authentication->getConnection();
        $sth               = $connection->prepare("$formatted_query");
        $executionSuccess  = $sth->execute($bound_variables);
        $executionEvent    = MySqlQueryExecutionEvent::init()
                                                     ->setQuery($query_or_statement)
                                                     ->setFormattedQuery($formatted_query)
                                                     ->setQueryVariables($bound_variables)
                                                     ->setDatabaseHandle($connection)
                                                     ->setStatementHandle($sth)
                                                     ->setExecutionSuccess($executionSuccess);
        
        if ($this->logQueries) {
            $this->getMonitor(static::MONITOR__QUERY_EXECUTED)->append($executionEvent);
            
            $displayContext = new SqlDisplayContext;
            $formattedQuery = $this->format($query_or_statement, $displayContext);
            
            $executionEvent->setFormattedQueryWithInlineVariables($formattedQuery);
        }
        
        return $executionEvent;
    }
    /**
     * @param \Sm\Modules\Query\Sql\Event\SqlQueryExecutionEvent $queryExecutionEvent
     * @param string                                             $return_type The way we want data returned
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @internal param mixed $query_or_statement The thing that we just executed
     * @internal param \PDOStatement $sth Connection to the database after executing the query
     */
    protected function interpretResult(SqlQueryExecutionEvent $queryExecutionEvent, $return_type) {
        $sth = $queryExecutionEvent->getStatementHandle();
        
        if (!($sth instanceof \PDOStatement)) {
            throw new InvalidArgumentException("Expected the handle to be a PDOStatement - " . Util::getShape($sth) . " given");
        }
        
        if ($return_type === SqlQueryInterpreter::RETURN_TYPE__SUCCESS) {
            return $queryExecutionEvent->getExecutionSuccess();
        }
        
        if ($return_type === SqlQueryInterpreter::RETURN_TYPE__LAST_INSERT_ID) {
            $dbh = $queryExecutionEvent->getDatabaseHandle();
            if (!($dbh instanceof \PDO)) {
                throw new InvalidArgumentException("Expected the handle to be a \PDO - " . Util::getShape($sth) . " given");
            }
            $id = $dbh->lastInsertId();
            return $id ? (int)$id : null;
        }
        
        
        # Don't fetch anything
        if ($return_type === false) return null;
        
        # If there are no columns, this query modifies rows & doesn't return a rowset
        if ($sth->columnCount() === 0) {
            return $sth->rowCount();
        }
        
        # Return the resultSet
        if ($return_type === 'auto') {
            return $sth->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        throw new UnimplementedError("No other return types are supported yet");
    }
    protected function getBoundVariables(SqlExecutionContext $formattingContext): array {
        $variables = $formattingContext->getVariables();
        if (!is_array($variables)) return [];
        
        $is_associative = null;
        
        $returned_variables = [];
        
        foreach ($variables as $index => $val) {
            $is_numeric = is_numeric($index);
            if ($is_associative === true && $is_numeric) throw new InvalidArgumentException("Cannot mix named and unnamed placeholders ");
            if ($is_associative === false && !$is_numeric) throw new InvalidArgumentException("Cannot mix named and unnamed placeholders ");
            $is_associative = !$is_numeric;
            
            if ($is_numeric) $returned_variables[] = $val;
            else $returned_variables[":$index"] = $val;
            
            
        }
        
        return $returned_variables;
    }
    
    public function getQueryMonitor(): Monitor {
        return $this->getMonitor(static::MONITOR__QUERY_EXECUTED);
    }
}