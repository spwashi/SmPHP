<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 4:44 PM
 */

namespace Sm\Query\Interpretation;

use Sm\Core\Internal\Monitor\Monitored;

/**
 * Class QueryInterpreter
 *
 * Class responsible for executing a Query and producing a relevant result
 *
 * @package Sm\Query\Interpretation
 */
interface QueryInterpreter extends Monitored {
    /**
     * Given something, execute that thing as a Query and return a result
     *
     * @param $query_or_statement
     *
     * @return mixed
     */
    public function interpret($query_or_statement);
}