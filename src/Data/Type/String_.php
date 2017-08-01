<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:17 PM
 */

namespace Sm\Data\Type;


use Sm\Core\Resolvable\StringResolvable;

class String_ extends StandardType {
    public static function resolveType($subject) {
        return StringResolvable::init($subject);
    }
}