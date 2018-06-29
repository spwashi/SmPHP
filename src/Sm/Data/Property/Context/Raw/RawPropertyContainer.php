<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/27/18
 * Time: 2:05 PM
 */

namespace Sm\Data\Property\Context\Raw;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Model\Resolvable\RawModelPropertyResolvable;
use Sm\Data\Property\Context\Raw\RawProperty;
use Sm\Data\Property\PropertyContainer;

class RawPropertyContainer extends PropertyContainer {
	public function set($name, $value = null) {
		if (is_iterable($name)) {

			# If we've been passed an iterable, add each property to this container
			foreach ($name as $key => $val) $this->set($key, $val);

			return $this;
		}

		# This might be false if we've accidentally passed an associative array in
		if (!is_string($name)) throw new InvalidArgumentException("Can properties with strings as names");

		# Register the raw property
		$this->register($name, RawProperty::init());

		# Set the value of the property
		$this->resolve($name)->value = RawModelPropertyResolvable::init($value);

		return $this;
	}
}