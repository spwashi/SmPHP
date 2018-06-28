<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/27/18
 * Time: 2:05 PM
 */

namespace Sm\Data\Property\Context;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Property\Exception\NonexistentPropertyException;
use Sm\Data\Property\PropertyContainer;

class RawPropertyContainer extends PropertyContainer {
	public function set($name, $value = null) {
		if (is_array($name)) {
			foreach ($name as $key => $val) {
				$this->set($key, $val);
			}
		} else if (!is_string($name)) {
			throw new InvalidArgumentException("Can properties with strings as names");
		}

		$this->register($name, RawProperty::init());
		$property        = $this->resolve($name);
		$property->value = RawProperty::init($value);
		return $this;
	}
}