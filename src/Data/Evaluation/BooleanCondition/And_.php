<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 8:13 AM
 */

namespace Sm\Data\Evaluation\BooleanCondition;

use Sm\Core\Exception\TypeMismatchException;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Util;

/**
 * Class And_
 *
 * Represents the boolean "And" construct. Accepts multiple items
 *
 * @package Sm\Data\Evaluation\Constructs
 */
class And_ extends BooleanCondition {
    protected function evaluateWithDefault(...$items) {
        # Return false if any of the values are falsey
        foreach ($items as $item) {
            if (is_string($item)) {
                if (!strlen($item)) return false;
                continue;
            }
            
            if (is_scalar($item) || is_bool($item)) {
                if (!boolval($item)) return false;
                continue;
            }
            
            if (!is_object($item) || !($item instanceof Resolvable)) throw new TypeMismatchException("Cannot evaluate items of type " . Util::getShapeOfItem($item));
            
            if (!$item->resolve()) return false;
        }
        return true;
    }
}