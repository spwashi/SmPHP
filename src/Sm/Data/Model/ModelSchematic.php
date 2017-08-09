<?php


namespace Sm\Data\Model;


use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Core\SmEntity\StdSmEntitySchematicTrait;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\Property\PropertySchemaContainer;

/**
 * Class ModelSchematic
 *
 * Represents the structure of a Model
 */
class ModelSchematic implements ModelSchema, SmEntitySchematic, \JsonSerializable {
    protected $protoSmID = '[Model]';
    
    use StdSmEntitySchematicTrait {
        load as protected _load_std;
    }
    
    
    protected $properties;
    /** @var \Sm\Data\Property\PropertyDataManager $propertyDataManager The SmEntityDataManager that will help configure PropertySchemas for us */
    private $propertyDataManager;
    /**
     * ModelSchematic constructor.
     *
     * @param \Sm\Data\Property\PropertyDataManager $propertyDataManager
     */
    public function __construct(PropertyDataManager $propertyDataManager) {
        $this->propertyDataManager = $propertyDataManager;
    }
    
    public static function init(PropertyDataManager $propertyDataManager) { return new static(...func_get_args()); }
    public function getProperties(): PropertySchemaContainer {
        return $this->properties = $this->properties ?? PropertySchemaContainer::init();
    }
    /**
     * @param mixed $properties
     *
     * @return ModelSchematic
     */
    public function setProperties(PropertySchemaContainer $properties) {
        $this->properties = $properties;
        return $this;
    }
    public function load($configuration) {
        $this->_load_std($configuration);
        $this->_configArraySet__properties($configuration);
        return $this;
    }
    
    protected function _configArraySet__properties($configuration) {
        $propertySchemaContainer = PropertySchemaContainer::init();
        $properties              = $configuration['properties'] ?? [];
        
        if (!count($properties)) return;
        
        # - convert the configurations to schematics
        $propertySchematic_array = [];
        foreach ($properties as $property_name => $property_config) {
            if (is_array($property_config)) $property_config['name'] = $property_config['name'] ?? $property_name;
            
            $propertySchematic                         = $this->propertyDataManager->configure($property_config);
            $propertySchematic_array[ $property_name ] = $propertySchematic;
        }
        
        # - register the properties
        $propertySchemaContainer->register($propertySchematic_array);
        
        #
        if (isset($propertySchemaContainer)) $this->setProperties($propertySchemaContainer);
    }
    public function jsonSerialize() {
        return [
            'smID'       => $this->getSmID(),
            'name'       => $this->getName(),
            'properties' => $this->properties,
        ];
    }
}