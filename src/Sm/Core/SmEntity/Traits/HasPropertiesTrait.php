<?php


namespace Sm\Core\SmEntity\Traits;


use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Schema\Schematic;
use Sm\Core\SmEntity\Exception\InvalidConfigurationException;
use Sm\Data\Property\Exception\NonexistentPropertyException;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertyHaverSchema;
use Sm\Data\Property\PropertyHaverSchematic;
use Sm\Data\Property\PropertySchemaContainer;
use Sm\Data\Property\PropertySchematic;
use Sm\Data\Property\Validation\PropertyValidationResult;

trait HasPropertiesTrait {
	protected function registerSchematicProperties(PropertyHaverSchematic $schematic): void {
		$propertyArray = [];
		foreach ($schematic->getProperties() as $index => $propertySchematic) {
			if (!$propertySchematic instanceof PropertySchematic) throw new InvalidConfigurationException('Expected to receive a schematic');

			if ($schematic instanceof PropertyHaver) {
				# If this is a PropertyHaver, we want to duplicate the Property and add it to this container
				$property = clone $propertySchematic;
			} else if ($schematic instanceof PropertyHaverSchematic) {
				# If we are iterating over the properties of a PropertyHaverSchematic, we want to instantiate the property
				$property = $this->instantiateProperty($propertySchematic);
			} else {
				throw new InvalidConfigurationException("Expected to register a PropertyHaver or Schematic");
			}

			$propertyArray[$index] = $property;
		}

		$this->getProperties()->register($propertyArray);
	}
	public function getProperties() {
		return $this->properties ?? new PropertyContainer;
	}
	public function setProperties(PropertyContainer $properties) {
		$this->properties = $properties;
		return $this;
	}
	public function validateProperties(Context $context = null): array {
		$propertyValidationResults = [];
		/** @var \Sm\Data\Entity\Property\EntityProperty $property */
		foreach ($this->properties as $property_identifier => $property) {
			try {
				if (!$property) throw new NonexistentPropertyException('Cannot set ' . $property_identifier . ' on User');
				$result                                          = $property->validate($context);
				$propertyValidationResults[$property_identifier] = $result;
			} catch (NonexistentPropertyException $exception) {
				$exception_msg                                   = $exception->getMessage();
				$propertyValidationResults[$property_identifier] = new PropertyValidationResult(false, $exception_msg);
			}
		}
		return $propertyValidationResults;
	}
	public function getPropertyValidationErrors(Context $context): array {
		$propertyValidationResults = $this->validateProperties($context);

		$property_errors = [];
		/** @var PropertyValidationResult $property_validationResult */
		foreach ($propertyValidationResults as $name => $property_validationResult) {
			if (isset($property_validationResult) && !$property_validationResult->isSuccess()) {
				$property_errors[$name] = $property_validationResult;
			}
		}
		return $property_errors;
	}
}