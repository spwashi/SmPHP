<?php


namespace Sm\Core\SmEntity\Traits;


use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Property\Exception\NonexistentPropertyException;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertyHaverSchema;
use Sm\Data\Property\PropertySchemaContainer;
use Sm\Data\Property\Validation\PropertyValidationResult;

trait HasPropertiesTrait {
    /**
     * @param \Sm\Data\Property\PropertyHaverSchema $schematic
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function registerSchematicProperties(PropertyHaverSchema $schematic): void {
        $propertySchemas = $schematic->getProperties();
        $propertyArray   = [];
        /** @var PropertyHaver $this */
        
        if (!($this instanceof PropertyHaverSchema)) {
            throw new InvalidArgumentException("Cannot instantiate a property on subjects that do not own properties");
        }
        
        foreach ($propertySchemas as $index => $propertySchema) {
            // Use the property if it exists on the PropertyHaver? Clone it
            if ($schematic instanceof PropertyHaver) {
                $property = clone $propertySchema;
            } else {
                $property = $this->instantiateProperty($propertySchema);
            }
            
            $propertyArray[ $index ] = $property;
        }
        
        try {
            $this->getProperties()->register($propertyArray);
        } catch (ReadonlyPropertyException $e) {
        }
    }
    public function getProperties(): PropertySchemaContainer {
        return $this->properties ?? new PropertySchemaContainer();
    }
    public function setProperties(PropertySchemaContainer $properties) {
        $this->properties = $properties;
        return $this;
    }
    /**
     * @param \Sm\Core\Context\Context|null $context
     *
     * @return array
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function validateProperties(Context $context = null): array {
        $propertyValidationResults = [];
        /** @var \Sm\Data\Entity\Property\EntityProperty $property */
        foreach ($this->properties as $property_identifier => $property) {
            try {
                if (!$property) throw new NonexistentPropertyException('Cannot set ' . $property_identifier . ' on User');
                $result                                            = $property->validate($context);
                $propertyValidationResults[ $property_identifier ] = $result;
            } catch (NonexistentPropertyException $exception) {
                $exception_msg                                     = $exception->getMessage();
                $propertyValidationResults[ $property_identifier ] = new PropertyValidationResult(false, $exception_msg);
            }
        }
        return $propertyValidationResults;
    }
    /**
     * @param \Sm\Core\Context\Context $context
     *
     * @return array
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function getPropertyValidationErrors(Context $context): array {
        $propertyValidationResults = $this->validateProperties($context);
        
        $property_errors = [];
        /** @var PropertyValidationResult $property_validationResult */
        foreach ($propertyValidationResults as $name => $property_validationResult) {
            if (isset($property_validationResult) && !$property_validationResult->isSuccess()) {
                $property_errors[ $name ] = $property_validationResult;
            }
        }
        return $property_errors;
    }
}