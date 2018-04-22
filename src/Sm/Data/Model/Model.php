<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:46 AM
 */

namespace Sm\Data\Model;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\Is_StdSchematicizedSmEntityTrait;
use Sm\Core\SmEntity\Is_StdSmEntityTrait;
use Sm\Core\SmEntity\SmEntity;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\Property;
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
    use Is_StdSmEntityTrait;
    use ModelTrait;
    use Is_StdSchematicizedSmEntityTrait {
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
    /**
     * @param      $name
     * @param null $value
     *
     * @return $this
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     */
    public function set($name, $value = null) {
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->set($key, $val);
            }
        } else {
            $this->properties->set($name, $value);
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
    public function markUnchanged() {
        /**
         * @var Property $property ;
         */
        foreach ($this->properties->getAll() as $property) {
            $property->resetValueHistory();
        }
    }
    public function getProperties(): PropertyContainer {
        return $this->properties = $this->properties ?? PropertyContainer::init();
    }
    public function setProperties(PropertyContainer $properties) {
        $this->properties = $properties;
        return $this;
    }
    public function registerProperty(string $name, Property $property = null) {
        try {
            $this->getProperties()->register($name, $property ?? new Property);
        } catch (InvalidArgumentException|ReadonlyPropertyException $e) {
        }
    }
    #
    ##  Configuration/Initialization
    
    /**
     * @param $modelSchematic
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     */
    public function fromSchematic($modelSchematic) {
        /** @var \Sm\Data\Model\ModelSchematic $modelSchematic */
        $this->_fromSchematic_std($modelSchematic);
        
        $this->setName($this->getName() ?? $modelSchematic->getName());
        $propertySchemas = $modelSchematic->getProperties();
        $propertyArray   = [];
        
        foreach ($propertySchemas as $index => $propertySchema) {
            $propertyArray[ $index ] = $modelSchematic->instantiateProperty($propertySchema);
        }
        
        $this->getProperties()->register($propertyArray);
        return $this;
    }
    /**
     * @param $schematic
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
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