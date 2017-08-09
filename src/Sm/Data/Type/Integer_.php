<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:17 PM
 */

namespace Sm\Data\Type;


use Sm\Core\Resolvable\NativeResolvable;

class Integer_ extends StandardDatatype {
    public static function resolveType($subject) {
        return NativeResolvable::init(intval($subject));
    }
}