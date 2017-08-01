<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:16 PM
 */

namespace Sm\Data\Type;


use Sm\Core\Resolvable\DateResolvable;

class DateTime_ extends StandardType {
    public function __toString() {
        return "" . $this->subject->resolve()->format('Y-m-d H:i:s.u');
    }
    public static function resolveType($subject) {
        return DateResolvable::init($subject);
    }
}