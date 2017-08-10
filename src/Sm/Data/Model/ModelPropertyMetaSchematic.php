<?php


namespace Sm\Data\Model;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Schema\Schematic;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchemaContainer;

class ModelPropertyMetaSchematic implements Schematic, \JsonSerializable {
    protected $propertyContainer;
    /** @var array $primarySmIDs an array of the smIDs that we say are primary */
    protected $primarySmIDs = [];
    public function __construct(PropertySchemaContainer $propertyContainer) {
        $this->propertyContainer = $propertyContainer;
    }
    public static function init(PropertySchemaContainer $propertySchemaContainer) {
        return new static($propertySchemaContainer);
    }
    
    public function load($configuration) {
        if (!is_array($configuration)) {
            throw new UnimplementedError("Cannot configure schematic using something other than an array");
        }
        $this->_configArraySet__primaryKeys($configuration);
        return $this;
    }
    public function isPrimary($propertySmID) {
        if ($propertySmID instanceof PropertySchema) {
            $propertySmID = $propertySmID->getSmID();
        }
        
        if (!$propertySmID) return false;
        
        if (!is_string($propertySmID)) throw new InvalidArgumentException("Can only check smIDs or Properties for primary status");
        
        return in_array($propertySmID, $this->primarySmIDs);
    }
    protected function _configArraySet__primaryKeys($configuration) {
        $primaries = $configuration['primary'] ?? [];
        if (!count($primaries)) return;
        $this->primarySmIDs = $primaries;
    }
    public function jsonSerialize() {
        return [
            'items' => $this->propertyContainer->getAll(),
            'meta'  => [
                'primarySmIDs' => $this->primarySmIDs,
            ],
        ];
    }
}