<?php


namespace Sm\Data\Model;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\SmEntity\StdSmEntityTrait;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\Property\PropertySchemaContainer;

/**
 * Class ModelSchematic
 *
 * Represents the structure of a Model
 */
class ModelSchematic implements ModelSchema {
    protected $name;
    protected $properties;
    /** @var \Sm\Data\Property\PropertyDataManager $propertyDataManager The SmEntityDataManager that will help configure PropertySchemas for us */
    private $propertyDataManager;
    use StdSmEntityTrait;
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
        if (!is_array($configuration)) throw new UnimplementedError("Cannot configure schematic without array");
        
        $name       = $this->_configArrayGet__name($configuration);
        $properties = $this->_configArrayGet__properties($configuration);
        
        if (isset($name)) $this->setName($name);
        if (isset($properties)) $this->setProperties($properties);
        return $this;
    }
    
    public function getName() { return $this->name; }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    protected function _configArrayGet__name($configuration) {
        return $configuration['name'] ?? null;
    }
    protected function _configArrayGet__properties($configuration): PropertySchemaContainer {
        $propertySchemaContainer = PropertySchemaContainer::init();
        $properties              = $configuration['properties'] ?? [];
        if (!count($properties)) return $propertySchemaContainer;
        
        # - convert the configurations to schematics
        $propertySchematic_array = [];
        foreach ($properties as $property_name => $property_config) {
            if (is_array($property_config)) $property_config['name'] = $property_config['name'] ?? $property_name;
            
            $propertySchematic                         = $this->propertyDataManager->configure($property_config);
            $propertySchematic_array[ $property_name ] = $propertySchematic;
        }
        
        # - register the properties
        $propertySchemaContainer->register($propertySchematic_array);
        return $propertySchemaContainer;
    }
}