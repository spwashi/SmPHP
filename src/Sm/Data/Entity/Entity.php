<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:46 AM
 */

namespace Sm\Data\Entity;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntity;
use Sm\Core\SmEntity\StdSchematicizedSmEntity;
use Sm\Core\SmEntity\StdSmEntityTrait;
use Sm\Data\Property\PropertyContainer;

/**
 * Class Entity
 *
 * Really a DAO (Data Access Object) but named Entity because of other MVC Frameworks
 *
 * Entitys represent a collection of Data, wherever they are, however they are stored.
 * Meant to abstract the basic operations that we will perform on Data, regardless
 * of if they are JSON, a row in a Table (most common) or some other form of Data.
 *
 * Each Entity should have a DataSource.
 *
 *
 * @package Sm\Data\Entity
 */
class Entity implements EntitySchema,
                        Schematicized,
                        SmEntity,
                        \JsonSerializable {
    use StdSmEntityTrait;
    use EntityTrait;
    use StdSchematicizedSmEntity {
        fromSchematic as protected _fromSchematic_std;
    }
    
    protected $properties;
    
    #
    ## Getters and Setters
    public function getProperties(): PropertyContainer {
        return $this->properties = $this->properties ?? PropertyContainer::init();
    }
    public function setProperties(PropertyContainer $properties) {
        $this->properties = $properties;
        return $this;
    }
    
    #
    ##  Configuration/Initialization
    public function fromSchematic($schematic) {
        /** @var \Sm\Data\Entity\EntitySchematic $schematic */
        $this->_fromSchematic_std($schematic);
        
        $this->setName($this->getName() ?? $schematic->getName());
        
        $pdm             = $schematic->getPropertyDataManager();
        $propertySchemas = $schematic->getProperties();
        $properties      = [];
        foreach ($propertySchemas as $index => $propertySchema) {
            $properties[ $index ] = $pdm->instantiate($propertySchema);
        }
        $this->getProperties()->register($properties);
        return $this;
    }
    public function checkCanUseSchematic($schematic) {
        if (!($schematic instanceof EntitySchematic)) {
            throw new InvalidArgumentException("Cannot use anything except for a Entity Schema to initialize these");
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