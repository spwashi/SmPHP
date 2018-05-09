<?php


namespace Sm\Data\Entity;


use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Core\SmEntity\Traits\HasPropertiesTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntitySchematicTrait;
use Sm\Data\Model\ModelDataManager;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\Property\PropertySchemaContainer;

/**
 * Class EntitySchematic
 *
 * Represents the structure of a Entity
 */
class EntitySchematic implements EntitySchema, SmEntitySchematic, \JsonSerializable {
    use HasPropertiesTrait;
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
        $entity->properties          = PropertySchemaContainer::init();
        return $entity;
    }
    use EntityTrait;
    use Is_StdSmEntitySchematicTrait {
        load as protected _load_std;
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
        $propertySchemaContainer = PropertySchemaContainer::init();
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
            $propertySchematic_array[ $property_name ] = $this->propertyDataManager->configure($property_config);
        }
        
        # - register the properties
        $propertySchemaContainer->register($propertySchematic_array);
        
        # add them to the Schematic
        $this->setProperties($propertySchemaContainer);
    }
    protected function _configArraySet__persistedIdentity($configuration) {
        $propertySchemaContainer = PropertySchemaContainer::init();
        $persistedIdentity       = $configuration['persistedIdentity'] ?? null;
        if (!isset($persistedIdentity)) {
            return;
        }
        
        $this->setPersistedIdentity($persistedIdentity);
    }
    #
    ##  Getters and Setters
    public function jsonSerialize() {
        $items = [
            'smID'              => $this->getSmID(),
            'name'              => $this->getName(),
            'properties'        => $this->getProperties()->getAll(),
            'persistedIdentity' => $this->getPersistedIdentity(),
        ];
        return $items;
    }
    /**
     * @param $persistedIdentity
     *
     * @return mixed
     */
    protected function setPersistedIdentity($persistedIdentity) {
        return $this->persistedIdentityName = $persistedIdentity;
    }
    public function hasPersistedIdentity() {
        return isset($this->persistedIdentityName);
    }
    public function getPersistedIdentity(): ?ModelSchema {
        /** @var ModelDataManager $modelDataManager */
        $modelDataManager = $this->modelDataManager;
        return $modelDataManager->getSchematicByName($this->persistedIdentityName);
    }
    public function __debugInfo() {
        return $this->jsonSerialize();
    }
}