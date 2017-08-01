<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 5:21 PM
 */

namespace Sm\Query\Modules\Sql\MySql;


use Sm\Authentication\Authentication;
use Sm\Authentication\Exception\InvalidAuthenticationException;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\TypeMismatchException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Util;
use Sm\Query\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Query\Modules\Sql\SqlQueryInterpreter;

/**
 * Class MySqlQueryInterpreter
 *
 * Query interpreter for statements that refer to items stored in a MySql database
 *
 * @package Sm\Query\Modules\Sql\MySql
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
        if (!($authentication instanceof MySqlAuthentication)) throw new TypeMismatchException("Can only connect with a MySqlAuthentication");
        $authentication->connect();
        if (!$authentication->isValid()) throw new InvalidAuthenticationException("The Authentication for this");
    }
    protected function execute(string $formatted_query) {
        $connection = $this->authentication->getConnection();
        $sth        = $connection->prepare("$formatted_query");
        $result     = $sth->execute();
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
        if (!($sth instanceof \PDOStatement)) throw new InvalidArgumentException("Expected the handle to be a PDOStatement - " . Util::getShapeOfItem($sth) . " given");
        
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
}