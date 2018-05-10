<?php


namespace Sm\Data\Entity\Property;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyFactory;
use Sm\Data\Type\DatatypeFactory;

class EntityPropertyFactory extends PropertyFactory {
    /**
     * @param null $parameters
     *
     * @return \Sm\Data\Property\Property
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function resolveDefault($parameters = null) {
        if (!isset($parameters)) {
            $property = new Property();
            return $property->setDatatypeFactory(new DatatypeFactory());
        } else {
            throw new InvalidArgumentException("Cannot instantiate model with parameters");
        }
    }
    
}