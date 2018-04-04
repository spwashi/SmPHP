<?php


namespace Sm\Data\Model;


use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Core\SmEntity\StdSmEntitySchematicTrait;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchemaContainer;

/**
 * Class ModelSchematic
 *
 * Represents the structure of a Model
 *
 * @property PropertySchemaContainer $properties
 */
class ModelSchematic implements ModelSchema,
                                SmEntitySchematic,
                                \JsonSerializable {
    use ModelTrait;
    use StdSmEntitySchematicTrait {
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
        $model->properties          = PropertySchemaContainer::init();
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
    ##  Getters and Setters
    public function getProperties(): PropertySchemaContainer {
        return $this->properties;
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
    
    #
    ##  Configuration
    /**
     * @param $configuration
     *
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    protected function _configArraySet__properties($configuration) {
        $propertySchemaContainer = PropertySchemaContainer::init();
        $properties              = $configuration['properties'] ?? [];
        $meta_config_array       = $configuration['propertyMeta'] ?? [];
        
        $this->propertyMeta = $this->createAndConfigureMeta($propertySchemaContainer, $meta_config_array);
        
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
        
        # add them to the ModelSchematic
        $this->setProperties($propertySchemaContainer);
    }
    
    /**
     *
     * @param \Sm\Data\Property\PropertySchema $propertySchema
     *
     * @return \Sm\Data\Property\Property
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function instantiateProperty(PropertySchema $propertySchema): Property {
        return $this->propertyDataManager->instantiate($propertySchema);
    }
    
    #
    ##  Serialization
    public function getPropertyMeta(): ModelPropertyMetaSchematic {
        return $this->propertyMeta;
    }
    /**
     * @param $propertySchemaContainer
     * @param $meta
     *
     * @return $this
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    protected function createAndConfigureMeta(PropertySchemaContainer $propertySchemaContainer, array $meta = []): ModelPropertyMetaSchematic {
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
}