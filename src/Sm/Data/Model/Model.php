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
use Sm\Data\Model\Context\ContextualizedModelProxy;
use Sm\Data\Model\Context\ModelSearchContext;
use Sm\Data\Model\Validation\ModelValidationResult;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertyInstantiator;
use Sm\Data\Property\Traits\IsSchematicizedPropertyHaver;
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
class Model implements ModelInstance,
                       Schematicized,
                       SmEntity,
                       Validatable,
                       \JsonSerializable {

	use Is_StdSmEntityTrait;
	use ModelTrait;

	use HasPropertiesTrait, IsSchematicizedPropertyHaver {
		HasPropertiesTrait::inheritingPropertyHaver insteadof IsSchematicizedPropertyHaver;
	}
	use Is_StdSchematicizedSmEntityTrait {
		fromSchematic as protected _fromSchematic_std;
	}

	/** @var  PropertyContainer */
	protected $properties;
	/** @var \Sm\Data\Property\PropertyInstantiator $propertySchematicInstantiator When we interact with properties, we need to know how to instantiate them */
	protected $propertySchematicInstantiator;

	#
	## Constructor
	public function __construct(PropertyInstantiator $propertySchematicInstantiator) {
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
	public function markUnchanged() {
		/**
		 * @var Property $property ;
		 */
		foreach ($this->properties->getAll() as $property) {
			$property->resetValueHistory();
		}
	}

	public function proxy(Context $context = null): ContextualizedModelProxy {
		if ($context instanceof ModelSearchContext) {
			var_dump('Ideally would return a specific kind of ContextualizedModelProxy');
			return new ContextualizedModelProxy($this, $context);
		}
		return new ContextualizedModelProxy($this, $context);
	}

	#
	## Validation
	public function validate(Context $context = null): ?ValidationResult {
		$property_errors = $this->getPropertyValidationErrors($context);

		return new ModelValidationResult(count($property_errors) < 1,
		                                 'model properties checked',
		                                 $property_errors);
	}

	#
	##  Configuration/Initialization
	public function fromSchematic($schematic) {
		/** @var ModelSchematic $schematic */

		# # # # standard initialization
		$this->_fromSchematic_std($schematic);

		# # # # name
		$this->setName($this->getName() ?? $schematic->getName());

		# # # # properties
		$schematicProperties = $schematic->getProperties();
		$this->properties->registerSchematics($schematicProperties);


		####
		return $this;
	}
	public function checkCanUseSchematic($schematic) {
		if (!$schematic instanceof ModelSchema) throw new InvalidArgumentException("Cannot use anything except for a Model Schema to initialize these");
	}

	#
	##  Debugging/Serialization
	public function __debugInfo() {
		return $this->jsonSerialize();
	}
	public function jsonSerialize() {
		$propertyContainer = $this->getProperties();
		$smID              = $this->getSmID();
		return [
			'smID'       => $smID,
			'name'       => $this->getName(),
			'properties' => $propertyContainer,
		];
	}

	#
	## Internal initializers
	protected function setPropertySchematicInstantiator(PropertyInstantiator $propertySchematicInstantiator) {
		$this->propertySchematicInstantiator = $propertySchematicInstantiator;
		$this->getProperties()->setPropertyInstantiator($propertySchematicInstantiator);
		return $this;
	}
}