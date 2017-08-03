<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 4:38 PM
 */

namespace Sm\Query\Interpretation;

use Sm\Core\Factory\StandardFactory;

/**
 * Class QueryInterpreterFactory
 *
 * Contains Quer
 *
 * @package Sm\Query\Interpretation
 */
class QueryInterpreterFactory extends StandardFactory {
    protected function canCreateClass($object_type) {
        return is_a($object_type, QueryInterpreter::class, true);
    }
}