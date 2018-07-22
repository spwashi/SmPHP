<?php


namespace Sm\Data\Entity;


use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Core\SmEntity\Traits\HasPropertySchematicsTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntitySchematicTrait;
use Sm\Data\Model\ModelDataManager;
use Sm\Data\Model\ModelSchematic;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\Property\PropertyHaverSchematic;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchematicContainer;

/**
 *
 *
 */
class EntitySchematic implements EntitySchema,
                                 PropertyHaverSchematic,
                                 SmEntitySchematic,
                                 \JsonSerializable {
	use HasPropertySchematicsTrait;
	use EntityTrait;
	use Is_StdSmEntitySchematicTrait {
		load as protected _load_std;
	}
	protected $datatypeFactory;
	protected $length;
	protected $onModelUpdateValue;
	protected $defaultValue;
	protected $isGenerated = false;
	/** @var ModelDataManager $modelDataManager */
	protected $modelDataManager;
	/** @var PropertyDataManager $propertyDataManager */
	protected $propertyDataManager;
	/** @var string $persistedIdentity */
	protected $persistedIdentity;

	protected function __construct() { }

	#
	##  Constructors/Initialization
	public static function init(ModelDataManager $modelDataManager, PropertyDataManager $propertyDataManager): EntitySchematic {
		$entity                      = new static;
		$entity->modelDataManager    = $modelDataManager;
		$entity->propertyDataManager = $propertyDataManager;
		$entity->properties          = PropertySchematicContainer::init();
		return $entity;
	}


	#
	##  Configuration
	public function load($configuration) {
		$this->_load_std($configuration);
		$this->_configArraySet__properties($configuration);
		$this->_configArraySet__persistedIdentity($configuration);
		return $this;
	}
	protected function _configArraySet__properties($configuration) {
		$propertySchemaContainer = PropertySchematicContainer::init();
		$properties              = $configuration['properties'] ?? [];
		if (!count($properties)) {
			return;
		}

		# - convert the configurations to schematics
		$propertySchematic_array = [];
		foreach ($properties as $property_name => $property_config) {
			if (is_array($property_config)) {
				$property_config['name'] = $property_config['name'] ?? $property_name;
			}
			$propertySchematic_array[$property_name] = $this->propertyDataManager->configure($property_config);
		}

		# - register the properties
		$propertySchemaContainer->register($propertySchematic_array);
		# add them to the Schematic
		$this->setProperties($propertySchemaContainer);
	}
	protected function _configArraySet__persistedIdentity($configuration) {
		$persistedIdentity = $configuration['persistedIdentity'] ?? null;
		if (!isset($persistedIdentity)) {
			return;
		}

		$this->setPersistedIdentity($persistedIdentity);
	}

	#
	##  Getters and Setters
	protected function setPersistedIdentity(string $persistedIdentity) {
		return $this->persistedIdentity = $persistedIdentity;
	}
	public function hasPersistedIdentity() {
		return isset($this->persistedIdentity);
	}
	public function getPersistedIdentity(): ?ModelSchematic {
		/** @var ModelDataManager $modelDataManager */
		$modelDataManager = $this->modelDataManager;
		return $modelDataManager->getSchematicByName($this->persistedIdentity);
	}

	#
	##  Serialization
	public function jsonSerialize() {
		$items = [
			'smID'              => $this->getSmID(),
			'name'              => $this->getName(),
			'properties'        => $this->getProperties()->getAll(),
			'persistedIdentity' => $this->getPersistedIdentity()->getSmID(),
		];
		return $items;
	}
	public function __debugInfo() {
		return $this->jsonSerialize();
	}
}