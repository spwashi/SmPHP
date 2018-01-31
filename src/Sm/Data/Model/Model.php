<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:46 AM
 */

namespace Sm\Data\Model;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntity;
use Sm\Core\SmEntity\StdSchematicizedSmEntity;
use Sm\Core\SmEntity\StdSmEntityTrait;
use Sm\Data\Property\PropertyContainer;

/**
 * Class Model
 *
 * Really a DAO (Data Access Object) but named Model because of other MVC Frameworks
 *
 * Models represent a collection of Data, wherever they are, however they are stored.
 * Meant to abstract the basic operations that we will perform on Data, regardless
 * of if they are JSON, a row in a Table (most common) or some other form of Data.
 *
 * Each Model should have a DataSource.
 *
 *
 * @package Sm\Data\Model
 * @property PropertyContainer $properties
 */
class Model implements ModelSchema,
                       Schematicized,
                       SmEntity,
                       \JsonSerializable {
    use StdSmEntityTrait;
    use ModelTrait;
    use StdSchematicizedSmEntity {
        fromSchematic as protected _fromSchematic_std;
    }
    /** @var  PropertyContainer */
    protected $properties;
    
    #
    ## Getters and Setters
    public function __get($name) {
        switch ($name) {
            case 'properties':
                return $this->getProperties();
            default:
                return null;
        }
    }
    public function set($name, $value = null) {
        if (is_array($name) && isset($value)) {
            throw new UnimplementedError("Not sure what to do with a name and value");
        }
        
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->set($key, $val);
            }
        } else {
            $this->properties->$name = $value;
        }
        return $this;
    }
    public function getChanged() {
        $changed_properties = [];
        
        /** @var \Sm\Data\Property\Property $property */
        foreach ($this->properties->getAll() as $propertyName => $property) {
            if ($property->valueHistory->count()) {
                $changed_properties[ $propertyName ] = $property;
            }
        }
        
        return $changed_properties;
    }
    
    public function getProperties(): PropertyContainer {
        return $this->properties = $this->properties ?? PropertyContainer::init();
    }
    public function setProperties(PropertyContainer $properties) {
        $this->properties = $properties;
        return $this;
    }
    
    #
    ##  Configuration/Initialization
    public function fromSchematic($modelSchematic) {
        /** @var \Sm\Data\Model\ModelSchematic $modelSchematic */
        $this->_fromSchematic_std($modelSchematic);
        
        $this->setName($this->getName() ?? $modelSchematic->getName());
        
        $propertyDataManager = $modelSchematic->getPropertyDataManager();
        $propertySchemas     = $modelSchematic->getProperties();
        $propertyArray       = [];
        
        foreach ($propertySchemas as $index => $propertySchema) {
            $propertyArray[ $index ] = $propertyDataManager->instantiate($propertySchema);
        }
    
        $this->getProperties()->register($propertyArray);
        return $this;
    }
    public function checkCanUseSchematic($schematic) {
        if (!($schematic instanceof ModelSchematic)) {
            throw new InvalidArgumentException("Cannot use anything except for a Model Schema to initialize these");
        }
    }
    
    
    #
    ##  Debugging/Serialization
    public function jsonSerialize() {
        $propertyContainer = $this->getProperties();
        return [
            'smID'       => $this->getSmID(),
            'name'       => $this->getName(),
            'properties' => $propertyContainer->count() ? $propertyContainer : null,
        ];
    }
    public function __debugInfo() {
        return $this->jsonSerialize();
    }
}