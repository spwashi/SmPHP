<?php
namespace Sm\Data\Entity\Property;
use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\Exception\UnresolvableException;
use Sm\Core\Resolvable\Resolvable;
use Sm\Data\Entity\Context\EntityCreationContext;
use Sm\Data\Entity\Context\NestedEntityCreationContext;
use Sm\Data\Entity\Entity;
use Sm\Data\Evaluation\Validation\ValidationResult;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\SmEntity\SmEntityDataManager;
use Sm\Data\Type\Undefined_;
/**
 * @property-read Entity $entity
 * @property-read PropertyContainer $identity
 */
class EntityAsProperty extends EntityProperty {
    /**
     * @var bool Have we "found" the entity?
     */
    protected $found = FALSE;
    /** @var Entity $subject */
    protected $subject;
    protected $attempted_validation_contexts = [];
    /** @var PropertyContainer */
    protected $identityPropertyContainer;
    #
    ##  Resolution
    public function resolve() {
        return $this->subject instanceof Resolvable ? $this->subject->resolve() : $this->subject;
    }

    #
    ##  Persistence
    public function find() {
        if ($this->found) {
            return $this->subject;
        } else {
            $this->found   = TRUE;
            $allProperties = $this->identityPropertyContainer->getAll();

            return $this->subject->find($allProperties);
        }
    }
    public function create(Context $context) {
        if ($this->subject) {
            $propertyContainer = $this->getIdentityProperties();
            $identityContainer = $propertyContainer->getAll();
            $this->subject->set($identityContainer);

            #
            ##  This ensures that we are creating in *a* creation context even if the last context wasn't
            ##      Might be useful if we are editing something in a ModificationContext or something
            ##      And that modification results in the creation of the Entiy associated with $this->subject

            $nested_creationContext = new NestedEntityCreationContext;
            $nested_creationContext->setParentContext($context);
            $nested_creationContext->setParentEntity($this->owner);

            # This should throw an error
            return $this->subject->create($nested_creationContext);
        } else {
            throw new UnresolvableException("Cannot create entity in this context");
        }
    }

    #
    ##  Validation
    public function validate(Context $context = NULL): ?ValidationResult {
        if ($this->subject) {
            $context_id = $context->getObjectId();
            if (isset($this->attempted_validation_contexts[$context_id])) {
                return $this->attempted_validation_contexts[$context_id];
            }
            $validationResult = $this->attempted_validation_contexts[$context_id] = $this->subject->validate($context);
            return $validationResult;
        }
        return NULL;
    }

    #
    ##  Getters and Setters
    public function __get($name) {
        switch ($name) {
            case 'entity':
                return $this->getEntity();
            case 'identity':
                return $this->initIdentityPropertyContainer();
            default:
                return parent::__get($name);
        }
    }
    public function setSubject($subject, $do_track_change = true) {
        if ($subject instanceof Undefined_) {
            return parent::setSubject($subject, $do_track_change);
        }
        $effectiveSchematic = $this->subject->getEffectiveSchematic();
        if (!$effectiveSchematic) {
            throw new UnimplementedError("Cannot set subject of entities without Schematics");
        }
        if (is_array($subject) && isset($subject['smID'])) {
            if (SmEntityDataManager::parseSmID($subject['smID']) == SmEntityDataManager::parseSmID($this->subject->getSmID())) {
                $this->subject->set($subject['properties'] ?? []);
                return;
            } else {
                throw new InvalidArgumentException('Wrong Entity Type! Whatchu doin here');
            }
        }
        /** @var \Sm\Data\Property\PropertySchemaContainer $propertySchematics */
        $propertySchematics = $effectiveSchematic->properties;
        foreach ($propertySchematics as $propertySchematic) {
            if (!($propertySchematic instanceof EntityPropertySchematic)) {
                continue;
            }
            if ($propertySchematic->getRole() === EntityPropertySchematic::ROLE__VALUE) {
                $propertySmID = $propertySchematic->getSmID();
                /** @var \Sm\Data\Property\Property $property */
                $property = $this->subject->properties->{$propertySmID};
                if ($property) {
                    $property->value = $subject;
                    return $this;
                }
            }
        }
        throw new UnimplementedError("Can only set subjects of entities that have a property with a 'value' role");
    }
    public function setEntity(Entity $entity) {
        $this->subject = $entity;
        return $this;
    }
    public function getEntity(): Entity {
        return $this->subject;
    }
    public function getValue(): Entity {
        return $this->subject;
    }

    #
    ##  Serialization
    public function __toString() {
        $value = $this->subject->components->value;
        return isset($value) ? "$value" : '[ string ]';
    }
    public function jsonSerialize() {
        return $this->subject;
    }
    protected function initIdentityPropertyContainer() {
        return $this->identityPropertyContainer = $this->identityPropertyContainer ?? new PropertyContainer;
    }
    protected function getIdentityProperties(): PropertyContainer {
        return $this->initIdentityPropertyContainer();
    }
}