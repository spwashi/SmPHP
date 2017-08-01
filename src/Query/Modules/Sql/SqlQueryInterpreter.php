<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 4:54 PM
 */

namespace Sm\Query\Modules\Sql;

use Sm\Authentication\AbstractAuthentication;
use Sm\Authentication\Authentication;
use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Interpretation\QueryInterpreter;
use Sm\Query\Modules\Sql\Formatting\SqlFormattingContext;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatterFactory;

/**
 * Class SqlQueryInterpreter
 *
 * Interprets Queries
 *
 * @package Sm\Query\Modules\Sql
 */
abstract class SqlQueryInterpreter extends QueryInterpreter {
    /** @var  \Sm\Query\Modules\Sql\Authentication\SqlAuthentication $authentication The thing that gives us credentials to use to connect to the database */
    protected $authentication;
    /** @var  Formatting\SqlQueryFormatterFactory $queryFormatter The thing that does the formatting */
    protected $queryFormatter;
    /**
     * SqlQueryInterpreter constructor.
     *
     * @param Authentication    $authentication The thing that will allow us to execute this Query
     * @param SqlQueryFormatter $queryFormatter The thing that will tell us how to format everything in the Query
     */
    public function __construct(Authentication $authentication, SqlQueryFormatterFactory $queryFormatter) {
        $this->setAuthentication($authentication);
        $this->queryFormatter = $queryFormatter;
    }
    
    public function interpret($query_or_statement, $return_type = 'auto', SqlFormattingContext $context = null) {
        $formatted_query = $this->format($query_or_statement, $context ?? new SqlExecutionContext);
        list($sth, $success) = $this->execute($formatted_query);
        if (!$success) throw new UnimplementedError("Better error name, also this was not successfully ");
        return $this->interpretResult($query_or_statement, $sth, $return_type);
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
     * @param \Sm\Query\Modules\Sql\Formatting\SqlFormattingContext|null $formattingContext The context in which we are formatting the query
     *
     * @return string
     */
    protected function format($query_or_statement, SqlFormattingContext $formattingContext = null): string {
        # If this is a string already, assume that it is already SQL
        if (is_string($query_or_statement)) return $query_or_statement;
        
        return $this->queryFormatter->format($query_or_statement, $formattingContext);
    }
    abstract protected function execute(string $formatted_query);
    /**
     * @param mixed  $query_or_statement The thing that we just executed
     * @param mixed  $sth                Connection to the database after executing the query
     * @param string $return_type        What the return type should look like
     *
     * @return mixed
     */
    abstract protected function interpretResult($query_or_statement, $sth, $return_type = 'auto');
}