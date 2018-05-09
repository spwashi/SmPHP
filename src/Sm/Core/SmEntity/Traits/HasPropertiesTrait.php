<?php


namespace Sm\Core\SmEntity\Traits;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertyHaverSchema;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchemaContainer;

trait HasPropertiesTrait {
    /**
     * @param \Sm\Data\Property\PropertyHaverSchema $schematic
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     */
    protected function registerSchematicProperties(PropertyHaverSchema $schematic): void {
        $propertySchemas = $schematic->getProperties();
        $propertyArray   = [];
        /** @var PropertyHaver $this */
        
        if (!($this instanceof PropertyHaverSchema)) {
            throw new InvalidArgumentException("Cannot instantiate a property on subjects that do not own properties");
        }
        
        foreach ($propertySchemas as $index => $propertySchema) {
            $propertyArray[ $index ] = $this->instantiateProperty($propertySchema);
        }
        
        $this->getProperties()->register($propertyArray);
    }
    public function getProperties(): PropertySchemaContainer {
        return $this->properties ?? new PropertySchemaContainer();
    }
    public function setProperties(PropertySchemaContainer $properties) {
        $this->properties = $properties;
        return $this;
    }
}