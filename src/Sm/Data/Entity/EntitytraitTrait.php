<?php


namespace Sm\Data\Entity;


use Sm\Core\Exception\InvalidArgumentException;

/**
 * Trait EntitytraitTrait
 *
 * Provides methods for easily referring to EntityTraits
 *
 * @package Sm\Data\Entity
 */
trait EntitytraitTrait {
	/** @var EntityHasPrimaryModelTrait|Entity $_entity */
	protected $_entity;

	# # # Allows us to refer to the _entity of a trait in a less confusing "this" oriented way
	protected function inheritingEntity(): Entity {
		if (isset($this->_entity)) return $this->_entity;
		if ($this instanceof Entity) return $this->_entity = $this;
		throw new InvalidArgumentException("Can not initialize Trait on non-entities");
	}
}