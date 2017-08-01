<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 4:44 PM
 */

namespace Sm\Query\Interpretation;

/**
 * Class QueryInterpreter
 *
 * Class responsible for executing a Query and producing a relevant result
 *
 * @package Sm\Query\Interpretation
 */
abstract class QueryInterpreter {
    /**
     * Given something, execute that thing as a Query and return a result
     *
     * @param $query_or_statement
     *
     * @return mixed
     */
    abstract public function interpret($query_or_statement);
}