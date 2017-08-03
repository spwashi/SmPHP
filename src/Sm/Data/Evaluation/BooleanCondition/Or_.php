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
 * Class Or_
 *
 * @package Sm\Data\Evaluation\Constructs
 */
class Or_ extends BooleanCondition {
    protected function evaluateWithDefault(...$items) {
        
        # Return true if any of the values are truthy
        foreach ($items as $item) {
            if (is_string($item)) {
                if (strlen($item)) return true;
                continue;
            }
            
            if (is_scalar($item) || is_bool($item)) {
                if (boolval($item)) return true;
                continue;
            }
            
            if (!is_object($item) || !($item instanceof Resolvable)) throw new TypeMismatchException("Cannot evaluate items of type " . Util::getShapeOfItem($item));
            
            if ($item->resolve()) return true;
        }
        return false;
    }
}