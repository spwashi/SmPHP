<?php
namespace Sm\Data\Entity;
use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Resolvable\Exception\UnresolvableException;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntity;
use Sm\Core\SmEntity\Traits\HasPropertiesTrait;
use Sm\Core\SmEntity\Traits\Is_StdSchematicizedSmEntityTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntityTrait;
use Sm\Data\Entity\Component\ComponentManager;
use Sm\Data\Entity\Property\EntityAsProperty;
use Sm\Data\Entity\Property\EntityProperty;
use Sm\Data\Entity\Property\EntityPropertyContainer;
use Sm\Data\Entity\Property\EntityPropertySchematic;
use Sm\Data\Model\Context\ContextualizedModelProxy;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelInstance;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\Exception\NonexistentPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertyHaverSchema;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\Traits\IsSchematicizedPropertyHaver;
use Sm\Data\Property\Validation\PropertyValidationResult;
use Sm\Data\Type\Undefined_;
/**
 * Class Entity
 *
 * Sort of a wrapper class for Models that have an identity we can verify. Bound to a ModelDataManager.
 *
 * @property-read PropertyContainer $properties
 * @property-read EntityDataManager $entityDataManager
 * @property-read EntitySchematic $schematic
 * @property-read ComponentManager $components
 *
 * @method EntitySchematic getEffectiveSchematic()
 */
abstract class Entity implements \JsonSerializable, EntitySchema, PropertyHaver, Schematicized, SmEntity, \Sm\Data\Evaluation\Validation\Validatable {
    use Is_StdSmEntityTrait;
    use HasMonitorTrait;
    use EntityTrait;
    use IsSchematicizedPropertyHaver;
    use Is_StdSchematicizedSmEntityTrait {
        fromSchematic as protected _fromSchematic_std;
    }

    protected $internal = [];
    /** @var bool $properties */
    private $properties = false;
    /** @var \Sm\Data\Entity\EntitySchematic */
    protected $effectiveSchematic;
    /** @var Model|ModelSchema $persistedIdentity */
    protected $persistedIdentity;
    /** @var ComponentManager */
    protected $components;

    #
    ## Instantiation/Initialization
    public function __construct(EntityDataManager $entityDataManager) {
        # Helps us better keep track of this Entity's internals
        $this->components = $this->initComponentManager($entityDataManager);
    }
    public static function init(EntityDataManager $entityDataManager) {
        return new static($entityDataManager);
    }
    protected function initComponentManager($entityDataManager): ComponentManager {
        return new ComponentManager($this, $entityDataManager);
    }

    #
    ## Schematic

    /** @param \Sm\Data\Entity\EntitySchematic $entitySchematic */
    public function fromSchematic($entitySchematic) {
        //  Stop caring about initialization
        $this->components->markInitializing();

        # # # # standard initialization
        $this->_fromSchematic_std($entitySchematic);

        # # # # name
        $this->setName($this->getName() ?? $entitySchematic->getName());

        # # # # persisted identities
        $persistedIdentitySchematic = $entitySchematic->hasPersistedIdentity() ? $entitySchematic->getPersistedIdentity() : null;
        $persistedIdentity          = $this->components->manager->modelDataManager->instantiate($persistedIdentitySchematic);
        $this->setPersistedIdentity($persistedIdentity);

        # # # # properties
        $this->components->registerSchematic($entitySchematic);

        //  We can start caring about changes again
        $this->components->markInitializingComplete();

        #####
        return $this;
    }

    /** Throws an error of we are trying to use an invalid schematic */
    protected function checkCanUseSchematic($schematic) {
        if (!$schematic instanceof EntitySchematic) throw new InvalidArgumentException("Can only initialize Entities using EntitySchematics");
    }

    #
    ## Getters and Setters
    public function __get($name) {
        switch ($name) {
            case 'schematic':
                return $this->getEffectiveSchematic();
            case 'properties':
                return $this->getProperties();
            case 'entityDataManager':
                return $this->components->manager;
            case 'components':
                return $this->components;
        }
        return null;
    }
    public function __call($name, $arguments) {
        switch ($name) {
            case 'properties':
                return FunctionResolvable::init([$this->components, 'resolveProperty']);
            default:
                throw new UnresolvableException("Cannot resolve {$name}");
        }
    }
    public function getProperties() {
        return $this->components->properties;
    }
    public function set($name, $value = null): Entity {
        if (is_array($name) && isset($value)) throw new UnimplementedError("Not sure what to do with a name and value");

        # Assume we are setting EntityProperties if we are accessing the object using this method
        if (is_string($name)) {
            $this->components->set($name, $value);
            return $this;
        }

        # Perhaps we should really check if this is iterable
        if (!is_iterable($name)) throw new InvalidArgumentException('Expected an associative array or a string');

        foreach ($name as $key => $val) $this->set($key, $val);

        return $this;
    }
    public function getPersistedIdentity(): ?ModelInstance {
        return $this->components->persisted_identity;
    }
    public function setPersistedIdentity(ModelInstance $model) {
        $this->components->setPersistedIdentity($model);
        return $this;
    }

    #
    ## Get/Set Properties
    public function markPropertyUpdated(Property $property) {
        $this->components->markComponentUpdated($property);
        return $this;
    }
    public function fillPropertyValue(Property $property, $value = Undefined_::class): void {
        /** @var \Sm\Data\Entity\Property\EntityPropertySchematic $propertySchematic */
        throw new UnimplementedError();
        $propertySchematic = $property->getEffectiveSchematic();
        if (!($propertySchematic instanceof EntityPropertySchematic)) {
            return;
        }
        /** @var Model $primaryModel */
        $primaryModel = $this->getPersistedIdentity();
        $derivedFrom  = $propertySchematic->getDerivedFrom();
        // Don't do anything if we're trying to set a value to itself
        if ($property === $value) return;
        // If we are trying to set the value to an entity property, use the value as the value
        if ($value instanceof EntityProperty) $value = $value->value;
        if ($value !== Undefined_::class) {
            $property->value = $value;
        } else if (is_string($derivedFrom)) {
            $property->value = $this->components->properties->{$derivedFrom} ?? $primaryModel->properties->{$derivedFrom};
        } else if (is_null($derivedFrom)) {
            return;
        } else if (!is_array($derivedFrom)) {
            throw new UnresolvableException("Cannot resolve anything but an association of properties");
        }
        if (is_array($derivedFrom)) {
            $identity         = $derivedFrom['identity'] ?? null;
            $hydration_method = $derivedFrom['hydrationMethod'] ?? null;
            if (isset($identity, $hydration_method)) {
                if (!isset($hydration_method['property'])) throw new UnimplementedError('Can only hydrate from properties');
                $property_hydration = $hydration_method['property'];
                if ($primaryModel) {
                    $modelProperty   = $primaryModel->properties->{$property_hydration};
                    $property->value = $modelProperty;
                }
            }
        }
        if (!($property instanceof EntityAsProperty)) {
            return;
        }
        if ($value !== Undefined_::class) $property->setSubject($value);
        $identity = [];
        foreach ($derivedFrom as $find_property_name => $value_property_smID) {
            $identity[$find_property_name] = $primaryModel->properties->{$value_property_smID};
        }
        $property->setIdentity($identity);
    }

    #
    ## CRUD     [all no-ops]
    public function save($attributes = []) { }
    public function find($attributes = [], Context $context = null) { }
    public function destroy() { }
    public function create(Context $context) { }

    #
    ## Serialization
    public function jsonSerialize() {
        return [
            'smID'       => $this->getSmID(),
            'properties' => $this->getProperties()->getAll(),
        ];
    }
}