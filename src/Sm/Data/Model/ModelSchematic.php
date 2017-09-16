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
class ModelSchematic implements ModelSchema,
                                SmEntitySchematic,
                                \JsonSerializable {
    use ModelTrait;
    use StdSmEntitySchematicTrait {
        load as protected _load_std;
    }
    
    
    protected $properties;
    protected $protoSmID = '[Model]';
    protected $propertyMeta;
    /** @var PropertyDataManager $propertyDataManager The SmEntityDataManager that will help configure PropertySchemas for us */
    private $propertyDataManager;
    
    #
    ##  Constructors/Initialization
    /**
     * ModelSchematic constructor.
     *
     * @param PropertyDataManager $propertyDataManager
     */
    public function __construct(PropertyDataManager $propertyDataManager) {
        $this->propertyDataManager = $propertyDataManager;
    }
    public static function init(PropertyDataManager $propertyDataManager) { return new static(...func_get_args()); }
    public function load($configuration) {
        $this->_load_std($configuration);
        $this->_configArraySet__properties($configuration);
        return $this;
    }
    
    #
    ##  Getters and Setters
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
    
    #
    ##  Configuration
    protected function _configArraySet__properties($configuration) {
        $propertySchemaContainer = PropertySchemaContainer::init();
        $properties              = $configuration['properties'] ?? [];
    
        $meta = $configuration['propertyMeta'] ?? null;
    
        $this->propertyMeta = ModelPropertyMetaSchematic::init($propertySchemaContainer)->load($meta);
        
        if (!count($properties)) return;
        
        # - convert the configurations to schematics
        $propertySchematic_array = [];
        foreach ($properties as $property_name => $property_config) {
    
            if (is_array($property_config)) $property_config['name'] = $property_config['name'] ?? $property_name;
    
            $name = $property_name;
    
            $propertySchematic                = $this->propertyDataManager->configure($property_config);
            $propertySchematic_array[ $name ] = $propertySchematic;
        }
        
        # - register the properties
        $propertySchemaContainer->register($propertySchematic_array);
        
        #
        if (isset($propertySchemaContainer)) $this->setProperties($propertySchemaContainer);
    }
    
    #
    ##  Serialization
    public function jsonSerialize() {
        return [
            'smID'       => $this->getSmID(),
            'name'       => $this->getName(),
            'properties' => $this->propertyMeta,
        ];
    }
    /**
     * @return PropertyDataManager
     */
    public function getPropertyDataManager(): PropertyDataManager {
        return $this->propertyDataManager;
    }
    public function getPropertyMeta(): ModelPropertyMetaSchematic {
        return $this->propertyMeta;
    }
}