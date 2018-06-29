<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:17 PM
 */

namespace Sm\Data\Type;


use Sm\Core\Resolvable\NullResolvable;

class Undefined_ extends StandardDatatype {
	public static function resolveType($subject) {
		return NullResolvable::init($subject);
	}
	public function __debugInfo() {
		return ['subject' => '~UNDEFINED~'];
	}
}