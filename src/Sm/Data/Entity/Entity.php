<?php


namespace Sm\Data\Entity;


use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Resolvable\Exception\UnresolvableException;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntity;
use Sm\Core\SmEntity\Traits\HasPropertiesTrait;
use Sm\Core\SmEntity\Traits\Is_StdSchematicizedSmEntityTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntityTrait;
use Sm\Data\Entity\Property\EntityAsProperty;
use Sm\Data\Entity\Property\EntityProperty;
use Sm\Data\Entity\Property\EntityPropertySchematic;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\Exception\NonexistentPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertyHaverSchema;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\Validation\PropertyValidationResult;
use Sm\Data\Type\Undefined_;

/**
 * Class Entity
 *
 * Sort of a wrapper class for Models that have an identity we can verify. Bound to a ModelDataManager.
 *
 * @property PropertyContainer $properties
 * @property EntityDataManager $entityDataManager
 */
abstract class Entity implements \JsonSerializable, EntitySchema, PropertyHaver, Schematicized, SmEntity, \Sm\Data\Evaluation\Validation\Validatable {
	use Is_StdSmEntityTrait;
	use HasPropertiesTrait;
	use HasMonitorTrait;
	use EntityTrait;
	use Is_StdSchematicizedSmEntityTrait {
		fromSchematic as protected _fromSchematic_std;
	}
	protected $internal = [];
	/** @var \Sm\Data\Entity\EntitySchematic */
	protected $effectiveSchematic;
	/** @var \Sm\Data\Entity\EntityDataManager */
	protected $entityDataManager;
	/** @var Model|ModelSchema $persistedIdentity */
	protected $persistedIdentity;

	#
	## Instantiation/Initialization
	public function __construct(EntityDataManager $entityDataManager) {
		$this->setEntityDataManager($entityDataManager);
	}
	public static function init(EntityDataManager $entityDataManager) {
		return new static($entityDataManager);
	}
	public function updateComponentProperties() {
		/**
		 * @var Property $property
		 */
		foreach ($this->properties->getAll() as $name => $property) {
			$effectiveSchematic = $property->getEffectiveSchematic();
			if (!($effectiveSchematic instanceof EntityPropertySchematic)) continue;
			$derivedFrom = $effectiveSchematic->getDerivedFrom();
			if (is_string($derivedFrom)) {
				$this->setInternalProperty($derivedFrom, $property->value);
			} else if (is_array($derivedFrom)) {
				foreach ($derivedFrom as $propertyName => $smID) {
					$value = $this->internal[$propertyName] ?? ($this->properties->$propertyName ? $this->properties->$propertyName->value : null);
					$this->setInternalProperty($smID, $value);
				}
			}
		}
	}

	#
	## Schematic
	/**
	 * @param $entitySchematic
	 *
	 * @return $this
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 */
	public function fromSchematic($entitySchematic) {
		/** @var \Sm\Data\Entity\EntitySchematic $entitySchematic */
		$this->_fromSchematic_std($entitySchematic);

		$this->setName($this->getName() ?? $entitySchematic->getName());
		if ($entitySchematic->hasPersistedIdentity()) {
			$this->persistedIdentity = $entitySchematic->getPersistedIdentity();
		}
		$this->registerSchematicProperties($entitySchematic);
		return $this;
	}
	/**
	 * @param $schematic
	 *
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 */
	protected function checkCanUseSchematic($schematic) {
		if (!($schematic instanceof EntitySchematic)) {
			throw new InvalidArgumentException("Can only initialize Entities using EntitySchematics");
		}
	}

	#
	## Getters and Setters
	public function __get($name) {
		switch ($name) {
			case 'properties':
				return $this->getProperties();
			case 'entityDataManager':
				return $this->entityDataManager;
		}
		return null;
	}
	/**
	 * @param string|array $name
	 * @param null         $value
	 *
	 * @return $this
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 */
	public function set($name, $value = null): Entity {
		if (is_array($name) && isset($value)) {
			throw new UnimplementedError("Not sure what to do with a name and value");
		}

		if (is_array($name)) {
			foreach ($name as $key => $val) {
				$this->set($key, $val);
			}
		} else {
			$persistedIdentity = $this->getPersistedIdentity();
			if ($this->properties->$name === null && $persistedIdentity && $persistedIdentity->getProperties()->$name) {
				$this->internal[$name] = $value;
			}
			if ($this->properties->$name) {
				$this->fillPropertyValue($this->properties->$name, $value);
			}
		}
		return $this;
	}
	/**
	 * @return mixed
	 */
	public function getPersistedIdentity(): ?ModelSchema {
		return $this->persistedIdentity;
	}
	/**
	 * @param \Sm\Data\Model\ModelSchema $modelSchema
	 *
	 * @return $this
	 */
	public function setPersistedIdentity(ModelSchema $modelSchema) {
		$this->persistedIdentity = $modelSchema;
		return $this;
	}
	protected function setInternalProperty(string $property_name_or_smID, $value) {
		$property = $this->properties->$property_name_or_smID;
		if (!isset($property)) {
			$modelSchema = $this->getPersistedIdentity();
			$properties  = $modelSchema->getProperties();
			$property    = $properties->{$property_name_or_smID};
		}
		$property->value = $value;
		return $property;
	}

	#
	## Get/Set Properties
	public function getProperties(): PropertyContainer {
		return $this->properties = $this->properties ?? PropertyContainer::init();
	}
	/**
	 * @param \Sm\Data\Property\PropertySchema $propertySchema
	 *
	 * @return \Sm\Data\Property\Property
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 */
	public function instantiateProperty(PropertySchema $propertySchema): Property {
		/** @var PropertyHaverSchema $self */
		$self = $this;
		if (!($self instanceof PropertyHaverSchema)) {
			throw new InvalidArgumentException("Cannot instantiate a property on subjects that do not own properties");
		}

		$propertyDataManager = $this->entityDataManager->getPropertyDataManager();
		$property            = $propertyDataManager->instantiate($propertySchema);
		return $property;
	}

	/**
	 * Given a property, set the value based on other properties in this entity or its primary model
	 *
	 * @param \Sm\Data\Property\Property $property
	 *
	 * @param string                     $value
	 *
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 * @throws InvalidArgumentException
	 */
	public function fillPropertyValue(Property $property, $value = Undefined_::class): void {
		/** @var \Sm\Data\Entity\Property\EntityPropertySchematic $propertySchematic */
		$propertySchematic = $property->getEffectiveSchematic();
		if (!($propertySchematic instanceof EntityPropertySchematic)) {
			return;
		}


		/** @var Model $primaryModel */
		$primaryModel = $this->getPersistedIdentity();
		$derivedFrom  = $propertySchematic->getDerivedFrom();

		// Don't do anything if we're trying to set a value to itself
		if ($property === $value) return;

		// If we are trying to set the value to an entity property, use the value as the value
		if ($value instanceof EntityProperty) $value = $value->value;

		if ($value !== Undefined_::class) {
			$property->value = $value;
		} else if (is_string($derivedFrom)) {
			$property->value = $this->properties->{$derivedFrom} ?? $primaryModel->properties->{$derivedFrom};
		} else if (is_null($derivedFrom)) {
			return;
		} else if (!is_array($derivedFrom)) {
			throw new UnresolvableException("Cannot resolve anything but an association of properties");
		}

		if (is_array($derivedFrom)) {
			$identity         = $derivedFrom['identity'] ?? null;
			$hydration_method = $derivedFrom['hydrationMethod'] ?? null;
			if (isset($identity, $hydration_method)) {
				if (!isset($hydration_method['property'])) throw new UnimplementedError('Can only hydrate from properties');

				$property_hydration = $hydration_method['property'];

				if ($primaryModel) {
					$modelProperty   = $primaryModel->properties->{$property_hydration};
					$property->value = $modelProperty;
				}
			}
		}


		if (!($property instanceof EntityAsProperty)) {
			return;
		}

		if ($value !== Undefined_::class) $property->setSubject($value);

		$identity = [];
		foreach ($derivedFrom as $find_property_name => $value_property_smID) {
			$identity[$find_property_name] = $primaryModel->properties->{$value_property_smID};
		}
		$property->setIdentity($identity);
	}

	#
	##
	public function validateProperties(Context $context): array {
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

	#
	## CRUD
	/**
	 * Save the Entity
	 *
	 * @param array $attributes The properties that we want to se on this Entity
	 *
	 * @return mixed
	 */
	abstract public function save($attributes = []);
	/**
	 * Find an Entity
	 *
	 * @param array      $attributes
	 * @param int|string $context What of this Entity we should find.
	 *
	 * @return mixed
	 */
	abstract public function find($attributes = [], Context $context = null);
	/**
	 * Destroy the Entity
	 */
	abstract public function destroy();

	#
	##
	protected function setEntityDataManager(EntityDataManager $entityDataManager) {
		$this->entityDataManager = $entityDataManager;
		return $this;
	}

	#
	##
	public function jsonSerialize() {
		return [
			'smID'       => $this->getSmID(),
			'properties' => $this->getProperties()->getAll(),
		];
	}
	/**
	 * @return array
	 */
	public function getInternal(): array {
		return $this->internal;
	}
}