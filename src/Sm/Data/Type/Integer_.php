<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:17 PM
 */

namespace Sm\Data\Type;


use Sm\Core\Resolvable\NativeResolvable;
use Sm\Core\Resolvable\Resolvable;

class Integer_ extends StandardDatatype {
	public static function resolveType($subject) {
		if ($subject instanceof Resolvable) {
			$subject = $subject->resolve();
		}

		$i = intval($subject);

		return NativeResolvable::init($i);
	}
}