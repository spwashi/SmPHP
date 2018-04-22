<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 4:54 PM
 */

namespace Sm\Modules\Query\Sql;

use Sm\Authentication\Authentication;
use Sm\Core\Exception\UnimplementedError;
use Sm\Modules\Query\Sql\Event\SqlQueryExecutionEvent;
use Sm\Modules\Query\Sql\Formatting\SqlFormattingContext;
use Sm\Modules\Query\Sql\Formatting\SqlQueryFormatter;
use Sm\Modules\Query\Sql\Formatting\SqlQueryFormatterManager;
use Sm\Modules\Query\Sql\Statements\CreateTableStatement;
use Sm\Query\Interpretation\QueryInterpreter;
use Sm\Query\Statements\InsertStatement;

/**
 * Class SqlQueryInterpreter
 *
 * Interprets Queries
 *
 * @package Sm\Modules\Query\Sql
 */
abstract class SqlQueryInterpreter implements QueryInterpreter {
    const RETURN_TYPE__LAST_INSERT_ID = 'LAST_INSERT_ID';
    const RETURN_TYPE__SUCCESS        = 'WAS_SUCCESSFUL';
    
    /** @var  \Sm\Modules\Query\Sql\Authentication\SqlAuthentication $authentication The thing that gives us credentials to use to connect to the database */
    protected $authentication;
    /** @var  Formatting\SqlQueryFormatterManager $formatterManager The thing that does the formatting */
    protected $formatterManager;
    /**
     * SqlQueryInterpreter constructor.
     *
     * @param Authentication    $authentication The thing that will allow us to execute this Query
     * @param SqlQueryFormatter $queryFormatter The thing that will tell us how to format everything in the Query
     */
    public function __construct(Authentication $authentication, SqlQueryFormatterManager $queryFormatter) {
        $this->setAuthentication($authentication);
        $this->formatterManager = $queryFormatter;
    }
    
    public function interpret($query_or_statement, $return_type = null) {
        $executionEvent = $this->execute($query_or_statement);
        
        switch ($return_type) {
            case null:
                if ($query_or_statement instanceof CreateTableStatement) {
                    $return_type = static::RETURN_TYPE__SUCCESS;
                } else if ($query_or_statement instanceof InsertStatement) {
                    $return_type = static::RETURN_TYPE__LAST_INSERT_ID;
                } else {
                    $return_type = 'auto';
                }
                break;
        }
        
        if (!$executionEvent->getExecutionSuccess()) {
            throw new UnimplementedError("Could not execute query (better error should be here)");
        }
        
        return $this->interpretResult($executionEvent, $return_type);
    }
    /**
     * Set the Authentication that we are going to use to connect to the Database & whatnot
     *
     * @param Authentication $authentication
     *
     * @return SqlQueryInterpreter
     */
    public function setAuthentication(Authentication $authentication): SqlQueryInterpreter {
        $this->checkAuthenticationValidity($authentication);
        $this->authentication = $authentication;
        return $this;
    }
    /**
     * Check to see if an Authentication can be used to interpret these Queries.
     * Throw an error on failure
     *
     * @param Authentication $authentication
     *
     * @return mixed
     */
    abstract public function checkAuthenticationValidity(Authentication $authentication);
    
    /**
     * See if we can convert the Statement or Clause or Query into something meaningful
     *
     * @param string|\Sm\Query\Statements\QueryComponent|mixed           $query_or_statement
     *
     * @param \Sm\Modules\Query\Sql\Formatting\SqlFormattingContext|null $sqlFormattingContext The context in which we are formatting the query
     *
     * @return string
     *
     */
    function format($query_or_statement, SqlFormattingContext $sqlFormattingContext = null): string {
        # If this is a string already, assume that it is already SQL
        if (is_string($query_or_statement)) return $query_or_statement;
        
        return $this->formatterManager->format($query_or_statement, $sqlFormattingContext);
    }
    abstract protected function execute($formatted_query): SqlQueryExecutionEvent;
    /**
     * @param \Sm\Modules\Query\Sql\Event\SqlQueryExecutionEvent $executionEvent
     *
     * @param                                                    $return_type
     *
     * @return mixed
     */
    abstract protected function interpretResult(SqlQueryExecutionEvent $executionEvent, $return_type);
}