<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:16 PM
 */

namespace Sm\Data\Type;


use Sm\Core\Resolvable\DateTimeResolvable;

class DateTime_ extends StandardDatatype {
	public function __toString() {
		return "" . $this->subject->resolve()->format(DATE_ISO8601);
	}
	public static function resolveType($subject) {
		return DateTimeResolvable::init($subject);
	}
}