<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/29/18
 * Time: 1:20 PM
 */

namespace Sm\Data\Entity\Property;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Entity\Entity;
use Sm\Data\Entity\Property\Exception\DetachedPropertyContainerException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyInstance;
use Sm\Data\Property\PropertySchema;

class EntityPropertyContainer extends PropertyContainer {
    /** @var Entity */
    protected $entity;
    public function setEntity(Entity $entity) {
        $this->entity = $entity;
        return $this;
    }
    public function instantiate($schematic = null): Property {
        return parent::instantiate($schematic);
    }
    protected function addToRegistry($name, $item) {
        if (!$item instanceof EntityProperty) throw new InvalidArgumentException("Can only add EntityProperties to this Container");
        if (!isset($this->entity)) throw new UnimplementedError("Cannot register properties without Entities");

        $item->setOwner($this->entity);

        $item->name = $item->name ?? $name;

        return parent::addToRegistry($name, $item);
    }

    public function resolve($name = null): ?PropertyInstance {
        if (!$this->entity) throw new DetachedPropertyContainerException('Expected an Entity where none was provided');

        return parent::resolve($name);
    }
}