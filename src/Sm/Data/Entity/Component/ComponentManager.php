<?php
namespace Sm\Data\Entity\Component;
use Sm\Core\Container\Mini\MiniContainer;
use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\Exception\UnresolvableException;
use Sm\Core\SmEntity\SmEntitySchema;
use Sm\Data\Entity\Entity;
use Sm\Data\Entity\EntityDataManager;
use Sm\Data\Entity\EntitySchematic;
use Sm\Data\Entity\Property\EntityAsProperty;
use Sm\Data\Entity\Property\EntityProperty;
use Sm\Data\Entity\Property\EntityPropertyContainer;
use Sm\Data\Entity\Property\EntityPropertySchematic;
use Sm\Data\Model\Context\ContextualizedModelProxy;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelInstance;
use Sm\Data\Property\Exception\NonexistentPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyContainerInstance;
use Sm\Data\Property\PropertySchematic;
use Sm\Data\Type\Undefined_;


/**
 * Manages the storage/proxying of an Entity's components
 *
 * @property-read Model $persisted_identity
 * @property-read Model $model
 * @property-read EntityDataManager $manager
 * @property-read EntityPropertyContainer $properties
 * @property-read Property $valueProperty
 * @property-read Mixed $value
 */
class ComponentManager {
    /** @var Model */
    protected $persisted_identity;
    /** @var Entity */
    protected $entity;
    /** @var EntityPropertyContainer $properties This is what will contain the properties that we directly set on the Entity */
    protected $properties;
    /** @var MiniContainer */
    protected $internalProperties;
    /** @var EntityDataManager $entityDataManager */
    protected $entityDataManager;
    protected $initializing = false;

    #
    ##  Constructors/Initialization
    public function __construct(Entity $entity, EntityDataManager $entityDataManager = null) {
        $this->entityDataManager = $entityDataManager ?? new EntityDataManager;
        $this->entity            = $entity;
        $this->initializeEntityPropertyContainer();
        $this->internalProperties = new MiniContainer;
    }
    protected function initializeEntityPropertyContainer(): PropertyContainer {
        $propertyManager   = $this->entityDataManager->getPropertyDataManager();
        $propertyContainer = EntityPropertyContainer::init();

        $propertyContainer->setPropertyInstantiator($propertyManager);
        $propertyContainer->setEntity($this->entity);

        $this->properties = $propertyContainer;

        return $propertyContainer;
    }

    #
    ##  Getters and Setters
    public function __get($name) {
        switch ($name) {
            case 'manager':
                return $this->entityDataManager;
            case 'value':
                $property = $this->getValueProperty();
                return $property ? $property->getValue() : Undefined_::init();
            case 'valueProperty':
                return $this->getValueProperty();
            case 'properties':
                return $this->properties;
            case 'persisted_identity':
            case 'model':
                return $this->getRepresentativeModel();
        }
        throw new InvalidArgumentException("Could not find {$name} in component Manager");
    }

    public function markInitializing() {
        $this->initializing = true;
        return $this;
    }
    public function markInitializingComplete() {
        $this->initializing = false;
        return $this;
    }
    public function markComponentUpdated($component) {
        if ($this->initializing) return $this;
        if (!($component instanceof SmEntitySchema)) throw new UnimplementedError("Cannot mark anything but SmEntities as being updated");

        $smID     = $component->getSmID();
        $property = $this->properties->resolve($smID);
        if (!$property) return $this;

        $this->updateProperties($property);

        return $this;
    }

    public function registerSchematic(EntitySchematic $entitySchematic) {
        return $this->properties->registerSchematics($entitySchematic->getProperties());
    }
    public function setPersistedIdentity(ModelInstance $model) {
        $model                    = $model instanceof ContextualizedModelProxy ? $model->getModel() : $model;
        $this->persisted_identity = $model;
        return $this;
    }
    /** Returns a Model that represents the Entity */
    public function getRepresentativeModel(Context $context = NULL): ?ContextualizedModelProxy {
        $model            = $this->instantiateModel();
        $model_properties = $this->getPropertiesForIdentityModel($context);
        $model->set($model_properties);
        return $model->proxy($context);
    }
    public function getPropertiesForIdentityModel(Context $context = null): PropertyContainerInstance {
        $model          = $this->instantiateModel();
        $properties     = [];
        $property_names = array_keys($this->properties->getAll());
        foreach ($property_names as $name) {
            $property          = $this->derivePropertyForModelFromEntity($name, $model, $context);
            $name              = $property ? $property->getName() ?? $name : $name;
            $properties[$name] = $property;
        }

        $not_null_fn = function ($item) { return isset($item); };
        $properties  = array_filter($properties, $not_null_fn);
        return PropertyContainer::init($properties);
    }

    #
    ##  Updating
    public function update() {
        $this->updateProperties();
    }
    protected function updateProperties($instigator = null): void {
        if ($instigator instanceof Property) {
            $this->updatePropertiesFromProperty($instigator);
            return;
        }


        /** @var EntityProperty $entityProperty */
        $properties = $this->properties->getAll();
        foreach ($properties as $name => $entityProperty) {

            /** @var EntityPropertySchematic $propertySchematic */
            $derivedFrom = $entityProperty->schematic->getDerivedFrom();

            if (is_array($derivedFrom)) {
                if (!$entityProperty instanceof EntityAsProperty) {
                    throw new UnimplementedError("Can only derive Entities from complex relationships");
                }

                foreach ($derivedFrom as $target_name => $derived_smID) {
                    $origin_property = $this->resolveProperty($derived_smID) ?? $this->resolveProperty($target_name);
                    $identity        = $entityProperty->identity;
                    // The property as we call it here might not have the same name property this EntityProperty refers to.
                    // Check the smID of the persisted identity to make sure we are referring to the same name
                    $actualProperty = $entityProperty->entity->components->resolveProperty($derived_smID);
                    $identity->register($actualProperty ? $actualProperty->getName() : $target_name,
                                        $origin_property);
                }

                continue;
            }

            if (!$entityProperty instanceof EntityAsProperty) continue;
        }
    }
    protected function updatePropertiesFromProperty(Property $instigator): void {
        # todo'


        /** @var EntityProperty $property */
        foreach ($this->properties as $property) {

            /** @var EntityPropertySchematic $propertySchematic */
            $propertySchematic = $property->schematic;
            $derivedFrom       = $propertySchematic->getDerivedFrom();

            if (!$property instanceof EntityAsProperty) continue;

            $propertyEntity = $property->entity;

            if (!$propertyEntity) continue;

            $valueProperty = $propertyEntity->components->valueProperty;

            if (!isset($valueProperty)) continue;
        }
    }

    #
    ##  Property resolution
    public function resolveProperty($name, Context $context = NULL): ?Property {
        ##  Should resolve a property at a particular time in a particular context
        $entity_property = $this->properties->$name;

        if (isset($entity_property)) return $entity_property;

        $modelProperties = $this->persisted_identity ? $this->persisted_identity->properties : null;
        if (!$modelProperties) return null;

        return $modelProperties->$name;
    }
    public function set($name, $value) {
        if (is_iterable($name)) {
            foreach ($name as $key => $val) $this->set($key, $val);
            return $this;
        }

        # todo think about whether it's a good thing that we set both the entity and model properties here

        $entityProperty = $this->properties->$name;
        if ($entityProperty) $entityProperty->setValue($value);

        $modelProperty = $this->persisted_identity->properties->resolve($name);
        if ($modelProperty) {
            $modelProperty->setValue($value);
        }

        # if the property hasn't been set yet, throw an exception
        if (!($modelProperty || $entityProperty)) {
            throw new NonexistentPropertyException("Cannot set {$name} on this Entity");
        }
    }


    #
    ##  Internal Getters and Setters
    /** Get the schematic of a property by its name */
    protected function getPropertySchematic($name): ?PropertySchematic {
        $properties = $this->entity->getProperties();
        $property   = $properties->$name;
        if (!$property) {
            throw new NonexistentPropertyException("Could not resolve $name on Entity");
        }
        /** @var Property $property */
        return $property->getEffectiveSchematic();
    }
    protected function instantiateModel(): Model {
        #
        ##  Initialize the Model
        $modelSchematic = $this->persisted_identity->getEffectiveSchematic();
        $model          = $this->entity->entityDataManager->modelDataManager->instantiate($modelSchematic);
        $model->set($this->persisted_identity->properties->getAll(), null);
        return $model;
    }
    protected function derivePropertyForModelFromEntity($name, ModelInstance $model = NULL, Context $context = null) {
        $model         = $model ?? $this->instantiateModel();
        $modelProperty = $model->properties->resolve($name);
        if (!$modelProperty) {
            /** @var EntityProperty $entityProperty */
            $entityProperty  = $this->entity->properties->resolve($name);
            $entity_derivers = $entityProperty->schematic->getDerivedFrom();
            if (count($entity_derivers) !== 1) {
                throw new UnresolvableException("Cannot resolve the Model property directly associated with {$name}");
            }

            $derived_name = array_keys($entity_derivers)[0];
            //  the model property doesn't exist
            if (!($modelProperty = $model->properties->resolve($derived_name))) {
                return NULL;
            }

            $propertyValue = $this->properties->resolve($name)->resolve();
            $property_name = $derived_name;
        } else {
            $propertyValue = $this->properties->resolve($name);
        }
        $newModelProperty = $model->properties->instantiate()->setValue($propertyValue)->setName($property_name ?? $name);
        return $newModelProperty;
    }

    public function getValueProperty(): ?Property {
        foreach ($this->properties as $property) {
            /** @var EntityProperty $property */
            if ($property->schematic->getRole() === EntityPropertySchematic::ROLE__VALUE) {
                return $property;
            }
        }

        return null;
    }
}