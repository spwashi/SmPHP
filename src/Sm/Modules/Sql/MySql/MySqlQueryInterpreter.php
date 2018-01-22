<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 5:21 PM
 */

namespace Sm\Modules\Sql\MySql;


use Sm\Authentication\Authentication;
use Sm\Authentication\Exception\InvalidAuthenticationException;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\TypeMismatchException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Util;
use Sm\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Sql\SqlExecutionContext;
use Sm\Modules\Sql\SqlQueryInterpreter;

/**
 * Class MySqlQueryInterpreter
 *
 * Query interpreter for statements that refer to items stored in a MySql database
 *
 * @package Sm\Modules\Sql\MySql
 */
class MySqlQueryInterpreter extends SqlQueryInterpreter {
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
    protected function execute($query_or_statement) {
        $formattingContext = new SqlExecutionContext;
        $formatted_query   = $this->format($query_or_statement, $formattingContext);
        $connection        = $this->authentication->getConnection();
        $variables         = $this->getVariables($formattingContext);
        $sth               = $connection->prepare("$formatted_query");
        $result            = $sth->execute($variables);
        return [ $sth, $result ];
    }
    /**
     * @param mixed         $query_or_statement The thing that we just executed
     * @param \PDOStatement $sth                Connection to the database after executing the query
     * @param string        $return_type        The way we want data returned
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    protected function interpretResult($query_or_statement, $sth, $return_type = 'auto') {
        if (!($sth instanceof \PDOStatement)) throw new InvalidArgumentException("Expected the handle to be a PDOStatement - " . Util::getShape($sth) . " given");
        
        # Don't fetch anything
        if ($return_type === false) return null;
        
        # If there are no columns, this query modifies rows & doesn't return a rowset
        if ($sth->columnCount() === 0) return $sth->rowCount();
        
        # Return the resultSet
        if ($return_type === 'auto') {
            if ($sth->rowCount() === 1) return $sth->fetch(\PDO::FETCH_ASSOC);
            return $sth->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        throw new UnimplementedError("No other return types are supported yet");
    }
    protected function getVariables(SqlExecutionContext $formattingContext): array {
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
}