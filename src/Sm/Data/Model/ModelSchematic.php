<?php


namespace Sm\Data\Model;


use Sm\Core\SmEntity\Exception\InvalidConfigurationException;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Core\SmEntity\Traits\HasPropertySchematicsTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntitySchematicTrait;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\Property\PropertyHaverSchematic;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchematicContainer;

class ModelSchematic implements ModelSchema,
                                PropertyHaverSchematic,
                                SmEntitySchematic,
                                \JsonSerializable {
	use ModelTrait;
	use HasPropertySchematicsTrait;
	use Is_StdSmEntitySchematicTrait {
		load as protected _load_std;
	}


	protected $properties;
	/** @var \Sm\Data\Model\ModelPropertyMetaSchematic $propertyMeta */
	protected $propertyMeta;
	/** @var PropertyDataManager $propertyDataManager The SmEntityDataManager that will help configure PropertySchemas for us */
	private $propertyDataManager;

	protected function __construct() { }

	#
	##  Constructors/Initialization
	public static function init(PropertyDataManager $propertyDataManager): ModelSchematic {
		$model                      = new static;
		$model->propertyDataManager = $propertyDataManager;
		$model->properties          = PropertySchematicContainer::init();
		return $model;
	}
	public function load($configuration) {
		$this->_load_std($configuration);
		$this->_configArraySet__properties($configuration);
		return $this;
	}
	public function __get($name) {
		switch ($name) {
			case 'properties':
				return $this->getProperties();
		}
	}

	#
	##  Configuration
	protected function _configArraySet__properties($configuration) {
		$schemaContainer    = PropertySchematicContainer::init();
		$properties         = $configuration['properties'] ?? [];
		$meta_config        = $configuration['propertyMeta'] ?? [];
		$this->propertyMeta = $this->createAndConfigureMeta($schemaContainer, $meta_config);

		if (!count($properties)) return;

		# - convert the configurations to schematics
		$propertySchematic_array = $this->propertyConfigToSchematics($properties);

		# - register the properties
		$schemaContainer->register($propertySchematic_array);

		# add them to the ModelSchematic
		$this->setProperties($schemaContainer);
	}
	public function instantiateProperty(PropertySchema $propertySchema): Property {
		return $this->propertyDataManager->instantiate($propertySchema);
	}
	protected function propertyConfigToSchematics($properties): array {
		$schematics = [];
		foreach ($properties as $property_name => $config) {

			if (!is_array($config)) {
				throw new InvalidConfigurationException('Can only configure using arrays');
			}

			$name                       = $config['name'] ?? $property_name;
			$config['name']             = $name;
			$schematic                  = $this->propertyDataManager->configure($config);
			$schematics[$property_name] = $schematic;
		}
		return $schematics;
	}

	#
	##  Serialization
	public function getPropertyMeta(): ModelPropertyMetaSchematic {
		return $this->propertyMeta;
	}
	protected function createAndConfigureMeta(PropertySchematicContainer $propertySchemaContainer, array $meta = []): ModelPropertyMetaSchematic {
		return ModelPropertyMetaSchematic::init($propertySchemaContainer)->load($meta);
	}

	public function jsonSerialize() {
		return [
			'smID'         => $this->getSmID(),
			'name'         => $this->getName(),
			'properties'   => $this->properties,
			'propertyMeta' => $this->propertyMeta,
		];
	}
	public function __debugInfo() {
		return $this->jsonSerialize();
	}
}