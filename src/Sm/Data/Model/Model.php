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
use Sm\Core\SmEntity\Traits\HasPropertiesTrait;
use Sm\Core\SmEntity\Traits\Is_StdSchematicizedSmEntityTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntityTrait;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchematicInstantiator;

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
                       PropertyHaver,
                       Schematicized,
                       SmEntity,
                       \JsonSerializable {
    use Is_StdSmEntityTrait;
    use HasPropertiesTrait;
    use ModelTrait;
    use Is_StdSchematicizedSmEntityTrait {
        fromSchematic as protected _fromSchematic_std;
    }
    
    /** @var  PropertyContainer */
    protected $properties;
    /** @var \Sm\Data\Model\ModelDataManager */
    private $propertySchematicInstantiator;
    
    public function __construct(PropertySchematicInstantiator $modelDataManager) {
        $this->propertySchematicInstantiator = $modelDataManager;
    }
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
    public function __clone() {
        $properties = $this->getProperties();
        $this->setProperties(clone $properties);
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
     * @param $schematic
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     */
    public function fromSchematic($schematic) {
        /** @var \Sm\Data\Model\ModelSchematic $schematic */
        $this->_fromSchematic_std($schematic);
        $this->setName($this->getName() ?? $schematic->getName());
        $this->registerSchematicProperties($schematic);
        return $this;
    }
    /**
     * @param $schematic
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function checkCanUseSchematic($schematic) {
        if (!($schematic instanceof ModelSchema)) {
            throw new InvalidArgumentException("Cannot use anything except for a Model Schema to initialize these");
        }
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
        return $this->propertySchematicInstantiator->instantiate($propertySchema);
    }
    
    #
    ##  Debugging/Serialization
    public function jsonSerialize() {
        $propertyContainer = $this->getProperties();
        $smID              = $this->getSmID();
        return [
            'smID'       => $smID,
            'name'       => $this->getName(),
            'properties' => $propertyContainer->count() ? $propertyContainer : null,
        ];
    }
    public function __debugInfo() {
        return $this->jsonSerialize();
    }
}