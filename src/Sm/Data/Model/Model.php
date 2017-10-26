<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:46 AM
 */

namespace Sm\Data\Model;


use Sm\Core\Exception\InvalidArgumentException;
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
        $properties          = [];
        foreach ($propertySchemas as $index => $propertySchema) {
            $properties[ $index ] = $propertyDataManager->instantiate($propertySchema);
        }
        $this->getProperties()->register($properties);
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