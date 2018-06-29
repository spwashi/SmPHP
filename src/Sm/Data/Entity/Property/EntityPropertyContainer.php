<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/29/18
 * Time: 1:20 PM
 */

namespace Sm\Data\Entity\Property;


use Sm\Data\Entity\Entity;
use Sm\Data\Entity\Property\Exception\DetachedPropertyContainerException;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertySchema;

class EntityPropertyContainer extends PropertyContainer {
	/** @var Entity */
	protected $entity;
	public function setEntity(Entity $entity) {
		$this->entity = $entity;
		return $this;
	}
	public function resolve($name = null): ?PropertySchema {
		if (!$this->entity) throw new DetachedPropertyContainerException('Expected an Entity where none was provided');

		return parent::resolve($name);
	}


}