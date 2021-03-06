<?php


namespace Sm\Data\Model;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Schema\Schematic;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchematicContainer;

/**
 * Class ModelPropertyMetaSchematic
 *
 * @property-read \Sm\Data\Property\PropertySchematicContainer $properties
 */
class ModelPropertyMetaSchematic implements Schematic, \JsonSerializable {
	protected $propertySchematicContainer;
	/** @var array $primarySmIDs an array of the smIDs that we say are primary */
	protected $primarySmIDs = [];
	/** @var  array $uniqueSmIDs An array of the smIDs of Properties that are unique across this Model. */
	protected $uniqueSmIDs = [];


	#
	##  Constructor/Initialization
	public function __construct(PropertySchematicContainer $propertyContainer) {
		$this->propertySchematicContainer = $propertyContainer;
	}
	public static function init(PropertySchematicContainer $propertySchemaContainer) {
		return new static($propertySchemaContainer);
	}
	public function __get($name) {
		switch ($name) {
			case 'properties':
				return $this->propertySchematicContainer;
		}
	}

	#
	##  Configuration
	public function load($configuration) {
		if (!$configuration) return $this;
		if (!is_array($configuration)) {
			throw new UnimplementedError("Cannot configure schematic using something other than an array");
		}
		$this->_configArraySet__primaryKeys($configuration);
		$this->_configArraySet__uniqueKeys($configuration);
		return $this;
	}
	protected function _configArraySet__primaryKeys($configuration) {
		$primaries = $configuration['primary'] ?? [];
		if (!count($primaries)) return;
		$this->primarySmIDs = $primaries;
	}
	protected function _configArraySet__uniqueKeys($configuration) {
		$unique = $configuration['unique'] ?? [];
		if (!count($unique)) return;
		$this->uniqueSmIDs = $unique['unique_key'] ?? null;
	}

	#
	##  Getters/Setters
	public function isPrimary($propertySmID) {
		$this->getPropertySmID($propertySmID);
		return in_array($propertySmID, $this->primarySmIDs);
	}
	public function getUniqueKeyGroup(): ?array {
		$uniqueKeys = $this->uniqueSmIDs;
		return $uniqueKeys;
	}
	/**
	 * @param $property
	 *
	 * @return bool
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 */
	protected function getPropertySmID(&$property): bool {
		if ($property instanceof PropertySchema) {
			$property = $property->getSmID();
		}

		if (!$property) return false;

		if (!is_string($property)) {
			throw new InvalidArgumentException("Can only check smIDs or Properties for primary status");
		}

		return true;
	}

	#
	##  Serialization
	public function jsonSerialize() {
		return [
			'primary' => $this->primarySmIDs,
		];
	}
}