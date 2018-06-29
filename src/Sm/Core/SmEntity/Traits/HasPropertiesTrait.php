<?php


namespace Sm\Core\SmEntity\Traits;


use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\TypeMismatchException;
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
	#
	##  Initialization
	/** @return PropertyContainer */
	protected function instantiatePropertyContainer() {
		return new PropertyContainer;
	}

	#
	##  Getters and Setters
	public function getProperties($property_names = []): PropertyContainer {
		$properties = $this->properties = $this->properties ?? $this->instantiatePropertyContainer();

		if (!$properties instanceof PropertyContainer) throw new TypeMismatchException("Expected a PropertyContainer where none was provided");

		if (count($property_names)) {
			$return_properties = [];

			foreach ($property_names as $name) $return_properties[$name] = $properties->{$name};

			return $this->instantiatePropertyContainer()->register($return_properties);
		}

		return $properties;
	}
	public function setProperties(PropertyContainer $properties) {

		$this->properties = $properties;

		return $this;
	}
	protected function registerSchematicProperties(PropertyHaverSchematic $ownerSchematic): void {
		$array      = [];
		$properties = $ownerSchematic->getProperties();

		foreach ($properties as $index => $schematic) {
			if (!$schematic instanceof PropertySchematic) throw new InvalidConfigurationException('Expected to receive a schematic');

			if ($ownerSchematic instanceof PropertyHaver) {
				# If this is a PropertyHaver, we want to duplicate the Property and add it to this container
				$property = clone $schematic;
			} else if ($ownerSchematic instanceof PropertyHaverSchematic) {
				# If we are iterating over the properties of a PropertyHaverSchematic, we want to instantiate the property
				$property = $this->instantiateProperty($schematic);
			} else {
				throw new InvalidConfigurationException("Expected to register a PropertyHaver or Schematic");
			}

			$array[$index] = $property;
		}

		$this->getProperties()->register($array);
	}

	#
	##  Validation
	public function validateProperties(Context $context = null): array {
		$propertyValidationResults = [];
		/** @var \Sm\Data\Entity\Property\EntityProperty $property */
		foreach ($this->properties as $property_identifier => $property) {
			try {
				if (!$property) throw new NonexistentPropertyException('Cannot set ' . $property_identifier . ' on this Entity');
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