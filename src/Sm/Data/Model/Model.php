<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:46 AM
 */

namespace Sm\Data\Model;


use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntity;
use Sm\Core\SmEntity\Traits\HasPropertiesTrait;
use Sm\Core\SmEntity\Traits\Is_StdSchematicizedSmEntityTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntityTrait;
use Sm\Data\Evaluation\Validation\Validatable;
use Sm\Data\Evaluation\Validation\ValidationResult;
use Sm\Data\Model\Validation\ModelValidationResult;
use Sm\Data\Property\Exception\NonexistentPropertyException;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchematicInstantiator;
use Sm\Data\Property\Validation\PropertyValidationResult;
use Sm\Data\Type\Undefined_;

/**
 * Class Model
 *
 * Really a DAO (Data Access Object) but named Model because of other MVC Frameworks
 *
 * Models represent a collection of Data, wherever they are, however they are stored.
 * Meant to abstract the basic operations that we will perform on Data, regardless
 * of if they are JSON, a row in a Table (most common) or some other form of Data.
 *
 * Each Model should have a DataSource.
 *
 *
 * @package Sm\Data\Model
 * @property PropertyContainer $properties
 */
class Model implements ModelSchema,
                       PropertyHaver,
                       Schematicized,
                       SmEntity,
                       Validatable,
                       \JsonSerializable {
	use Is_StdSmEntityTrait;
	use HasPropertiesTrait;
	use ModelTrait;
	use Is_StdSchematicizedSmEntityTrait {
		fromSchematic as protected _fromSchematic_std;
	}

	/** @var  PropertyContainer */
	protected $properties;
	/** @var \Sm\Data\Property\PropertySchematicInstantiator $propertySchematicInstantiator When we interact with properties, we need to know how to instantiate them */
	protected $propertySchematicInstantiator;

	#
	## Constructor
	public function __construct(PropertySchematicInstantiator $propertySchematicInstantiator) {
		$this->setPropertySchematicInstantiator($propertySchematicInstantiator);
	}

	#
	## Getters and Setters
	public function __get($name) {
		switch ($name) {
			case 'properties':
				return $this->getProperties();
			default:
				return null;
		}
	}
	public function __clone() {
		$properties = $this->getProperties();
		$this->setProperties(clone $properties);
	}

	#
	## Interact with Properties
	public function getChanged() {
		$changed_properties = [];

		/** @var \Sm\Data\Property\Property $property */
		foreach ($this->properties->getAll() as $propertyName => $property) {
			if ($property->value instanceof Undefined_) continue;

			if ($property->valueHistory->count()) {
				$changed_properties[$propertyName] = $property;
			}
		}

		return $changed_properties;
	}
	public function markUnchanged() {
		/**
		 * @var Property $property ;
		 */
		foreach ($this->properties->getAll() as $property) {
			$property->resetValueHistory();
		}
	}
	public function getProperties($property_names = []): PropertyContainer {
		$properties = $this->properties = $this->properties ?? PropertyContainer::init();

		if (count($property_names)) {
			$return_properties = [];
			foreach ($property_names as $name) {
				$return_properties[$name] = $properties->{$name};
			}
			return PropertyContainer::init()->register($return_properties);
		}

		return $properties;
	}
	public function setProperties(PropertyContainer $properties) {
		$this->properties = $properties;
		return $this;
	}
	public function registerProperty(string $name, Property $property = null) {
		try {
			$this->getProperties()->register($name, $property ?? $this->propertySchematicInstantiator->instantiate($name));
		} catch (InvalidArgumentException|ReadonlyPropertyException $e) {
		}
	}

	#
	## Validation
	/**
	 * @param \Sm\Core\Context\Context|null $context
	 *
	 * @return null|ModelValidationResult
	 * @throws \Sm\Core\Exception\UnimplementedError
	 */
	public function validate(Context $context = null): ?ValidationResult {
		$property_errors = $this->getPropertyValidationErrors($context);

		return new ModelValidationResult(count($property_errors) < 1,
		                                 'model properties checked',
		                                 $property_errors);
	}

	#
	##  Configuration/Initialization
	/**
	 * @param $schematic
	 *
	 * @return $this
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
	 */
	public function fromSchematic($schematic) {
		/** @var \Sm\Data\Model\ModelSchematic $schematic */
		$this->_fromSchematic_std($schematic);
		$this->setName($this->getName() ?? $schematic->getName());
		$this->registerSchematicProperties($schematic);
		return $this;
	}
	/**
	 * @param $schematic
	 *
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 */
	public function checkCanUseSchematic($schematic) {
		if (!($schematic instanceof ModelSchema)) {
			throw new InvalidArgumentException("Cannot use anything except for a Model Schema to initialize these");
		}
	}
	/**
	 * @param \Sm\Data\Property\PropertySchema $propertySchema
	 *
	 * @return \Sm\Data\Property\Property
	 */
	public function instantiateProperty(PropertySchema $propertySchema): Property {
		$property = $this->propertySchematicInstantiator->instantiate($propertySchema);

		return $property;
	}

	#
	##  Debugging/Serialization
	public function jsonSerialize() {
		$propertyContainer = $this->getProperties();
		$smID              = $this->getSmID();
		return [
			'smID'       => $smID,
			'name'       => $this->getName(),
			'properties' => $propertyContainer->count() ? $propertyContainer : null,
		];
	}
	public function __debugInfo() {
		return $this->jsonSerialize();
	}
	/**
	 * @param \Sm\Data\Property\PropertySchematicInstantiator $propertySchematicInstantiator
	 *
	 * @return  $this
	 */
	protected function setPropertySchematicInstantiator(PropertySchematicInstantiator $propertySchematicInstantiator) {
		$this->propertySchematicInstantiator = $propertySchematicInstantiator;
		return $this;
	}
}