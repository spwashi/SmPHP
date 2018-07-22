<?php


namespace Sm\Data\Property\Traits;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Property\PropertyHaver;
trait PropertyHaver_traitTrait {
	protected $_propertyHaver;

	/** @return  PropertyHaver|static */
	protected function inheritingPropertyHaver(): PropertyHaver {
		if (isset($this->_propertyHaver)) return $this->_propertyHaver;
		if ($this instanceof PropertyHaver) return $this->_propertyHaver = $this;
		throw new InvalidArgumentException("Can not initialize Trait on non-PropertyHavers");
	}
}