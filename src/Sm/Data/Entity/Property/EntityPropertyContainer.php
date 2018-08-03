<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/29/18
 * Time: 1:20 PM
 */

namespace Sm\Data\Entity\Property;


use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Entity\Entity;
use Sm\Data\Entity\Property\Exception\DetachedPropertyContainerException;
use Sm\Data\Property\Exception\NonexistentPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyInstance;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\Validation\PropertyValidationResult;

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

    public function validateProperties(Context $context = null): array {
        $propertyValidationResults = [];

        /** @var \Sm\Data\Entity\Property\EntityProperty $property */
        $allProperties = $this->getAll();
        foreach ($allProperties as $property_identifier => $property) {

            try {

                if (!$property) throw new NonexistentPropertyException('Cannot set ' . $property_identifier . ' on Entity');

                $result                                          = $property->validate($context);
                $propertyValidationResults[$property_identifier] = $result;

            } catch (NonexistentPropertyException $exception) {
                $exception_msg                                   = $exception->getMessage();
                $propertyValidationResults[$property_identifier] = new PropertyValidationResult(false, $exception_msg);
            }
        }

        return $propertyValidationResults;
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