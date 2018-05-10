<?php


namespace Sm\Data\Entity;


use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Resolvable\Error\UnresolvableException;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntity;
use Sm\Core\SmEntity\Traits\HasPropertiesTrait;
use Sm\Core\SmEntity\Traits\Is_StdSchematicizedSmEntityTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntityTrait;
use Sm\Data\Entity\Property\EntityAsProperty;
use Sm\Data\Entity\Property\EntityPropertySchematic;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertyHaverSchema;
use Sm\Data\Property\PropertySchema;

/**
 * Class Entity
 *
 * Sort of a wrapper class for Models that have an identity we can verify. Bound to a ModelDataManager.
 *
 * @property PropertyContainer $properties
 */
abstract class Entity implements \JsonSerializable, EntitySchema, PropertyHaver, Schematicized, SmEntity {
    use Is_StdSmEntityTrait;
    use HasPropertiesTrait;
    use HasMonitorTrait;
    use EntityTrait;
    use Is_StdSchematicizedSmEntityTrait {
        fromSchematic as protected _fromSchematic_std;
    }
    /** @var \Sm\Data\Entity\EntitySchematic */
    protected $effectiveSchematic;
    
    /** @var  \Sm\Data\Model\ModelDataManager $modelDataManager */
    protected $modelDataManager;
    /** @var \Sm\Data\Entity\EntityDataManager */
    protected $entityDataManager;
    /** @var Model|ModelSchema $persistedIdentity */
    protected $persistedIdentity;
    
    public function __construct(EntityDataManager $entityDataManager) {
        $this->setEntityDataManager($entityDataManager);
    }
    public static function init(EntityDataManager $entityDataManager) {
        return new static($entityDataManager);
    }
    /**
     * @param $entitySchematic
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     */
    public function fromSchematic($entitySchematic) {
        /** @var \Sm\Data\Entity\EntitySchematic $entitySchematic */
        $this->_fromSchematic_std($entitySchematic);
        
        $this->setName($this->getName() ?? $entitySchematic->getName());
        if ($entitySchematic->hasPersistedIdentity()) {
            $this->persistedIdentity = $entitySchematic->getPersistedIdentity();
        }
        $this->registerSchematicProperties($entitySchematic);
        return $this;
    }
    public function __get($name) {
        switch ($name) {
            case 'properties':
                return $this->getProperties();
        }
        return null;
    }
    /**
     * @param      $name
     * @param null $value
     *
     * @return $this
     * @throws \Sm\Core\Exception\UnimplementedError
     */
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
    public function getProperties(): PropertyContainer {
        return $this->properties = $this->properties ?? PropertyContainer::init();
    }
    /**
     * @param \Sm\Data\Property\PropertySchema $propertySchema
     *
     * @return \Sm\Data\Property\Property
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function instantiateProperty(PropertySchema $propertySchema): Property {
        /** @var PropertyHaverSchema $self */
        $self = $this;
        if (!($self instanceof PropertyHaverSchema)) {
            throw new InvalidArgumentException("Cannot instantiate a property on subjects that do not own properties");
        }
        
        $propertyDataManager = $this->entityDataManager->getPropertyDataManager();
        $property            = $propertyDataManager->instantiate($propertySchema);
        return $property;
    }
    /**
     * @param \Sm\Data\Property\Property $property
     *
     * @throws \Sm\Core\Resolvable\Error\UnresolvableException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    protected function fillPropertyValue(Property $property): void {
        /** @var \Sm\Data\Entity\Property\EntityPropertySchematic $propertySchematic */
        $propertySchematic = $property->getEffectiveSchematic();
        if (!($propertySchematic instanceof EntityPropertySchematic)) {
            return;
        }
        /** @var Model $primaryModel */
        $primaryModel = $this->getPersistedIdentity();
        $derivedFrom  = $propertySchematic->getDerivedFrom();
        
        if (is_string($derivedFrom)) {
            $property->value = $this->properties->{$derivedFrom} ?? $primaryModel->properties->{$derivedFrom};
        } else if (!is_array($derivedFrom)) {
            throw new UnresolvableException("Cannot resolve anything but an association of properties");
        }
        
        if (!($property instanceof EntityAsProperty)) {
            return;
        }
        
        $identity = [];
        foreach ($derivedFrom as $find_property_name => $value_property_smID) {
            $identity[ $find_property_name ] = $primaryModel->properties->{$value_property_smID};
        }
        $property->setIdentity($identity);
    }
    /**
     * Find an Entity
     *
     * @param array      $attributes
     * @param int|string $context What of this Entity we should find.
     *
     * @return mixed
     */
    abstract public function find($attributes = [], Context $context = null);
    
    /**
     * Save the Entity
     *
     * @param array $attributes The properties that we want to se on this Entity
     *
     * @return mixed
     */
    abstract public function save($attributes = []);
    abstract public function destroy();
    
    protected function setEntityDataManager(EntityDataManager $entityDataManager) {
        $this->entityDataManager = $entityDataManager;
        return $this;
    }
    /**
     * @param $schematic
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function checkCanUseSchematic($schematic) {
        if (!($schematic instanceof EntitySchematic)) {
            throw new InvalidArgumentException("Can only initialize Entities using EntitySchematics");
        }
    }
    public function jsonSerialize() {
        return [
            'smID'       => $this->getSmID(),
            'properties' => $this->getProperties()->getAll(),
        ];
    }
    /**
     * @return mixed
     */
    public function getPersistedIdentity(): ?ModelSchema {
        return $this->persistedIdentity;
    }
    public function setPersistedIdentity(ModelSchema $modelSchema) {
        $this->persistedIdentity = $modelSchema;
        return $this;
    }
}