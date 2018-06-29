<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/28/18
 * Time: 10:33 AM
 */

namespace Sm\Data\Property\Context\Raw;


use Sm\Core\Resolvable\Exception\UnresolvableException;
use Sm\Core\Resolvable\NativeResolvable;
class RawPropertyValueResolvable extends NativeResolvable {
	public function resolve() {
		throw new UnresolvableException("Cannot resolve Raw properties");
	}
	function jsonSerialize() {
		return $this->subject;
	}

}