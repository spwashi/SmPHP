<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:18 PM
 */

namespace Sm\Data\Type;


use Sm\Core\Resolvable\NullResolvable;

class Null_ extends StandardType {
    public static function resolveType($subject) {
        return NullResolvable::init($subject);
    }
}