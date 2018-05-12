<?php


namespace Sm\Data\Entity;


use Sm\Core\Context\Context;
use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Proxy\ContextualizedProxy;
use Sm\Core\Context\Proxy\StandardContextualizedProxy;
use Sm\Core\Proxy\Proxy;
use Sm\Core\Schema\Schematic;
use Sm\Data\Entity\Property\EntityPropertySchematic;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertySchemaContainer;
use Sm\Data\Property\PropertySchematic;


class ContextualizedEntityProxy extends StandardContextualizedProxy implements Proxy, ContextualizedProxy, \JsonSerializable, EntitySchema {
    /** @var \Sm\Data\Entity\EntitySchema */
    protected $subject;
    /** @var \Sm\Data\Entity\EntityContext $context */
    protected $context;
    /**
     * ContextualizedEntityProxy constructor.
     *
     * @param \Sm\Data\Entity\EntitySchema $entitySchema
     * @param null                         $context
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function __construct(EntitySchema $entitySchema = null, EntityContext $context = null) {
        parent::__construct($entitySchema, $context);
        if ($entitySchema instanceof EntitySchematic) $context->registerEntitySchematic($entitySchema);
    }
    public function getName() {
        if (!$this->subject) return null;
        return $this->subject->getName();
    }
    public function setName(string $name) {
        return $this;
    }
    public function getPersistedIdentity(): ?ModelSchema {
        return null;
    }
    /**
     * @return \Sm\Data\Property\PropertySchemaContainer
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function getProperties(): PropertySchemaContainer {
        $context = $this->context;
        if (!$this->subject) return new PropertySchemaContainer;
        $properties               = $this->subject->getProperties();
        $contextualizedProperties = [];
        /**
         * @var \Sm\Data\Property\Property $property
         */
        foreach ($properties as $propertyName => $property) {
            $effectiveSchematic = $property instanceof Schematic ? $property : $property->getEffectiveSchematic();
            if (!($effectiveSchematic instanceof EntityPropertySchematic)) {
                continue;
            }
            $contextNames = $effectiveSchematic->getContextNames();
            $contextName  = $context->getContextName();
            if (!$contextNames || in_array($contextName, $contextNames))
                $contextualizedProperties[ $propertyName ] = $property;
        }
        $propertySchemaContainer = new PropertySchemaContainer;
        try {
            $propertySchemaContainer->register($contextualizedProperties);
        } catch (ReadonlyPropertyException $e) {
        } finally {
            return $propertySchemaContainer;
        }
    }
    /**
     * Get an Identifier that will remain consistent for this particular
     * chunk of data across each SmFramework implementation
     *
     * @return null|string
     */
    public function getSmID(): ?string {
        if (!$this->subject) return null;
        return $this->subject->getSmID();
    }
    /**
     * @param \Sm\Core\Context\Context $context
     *
     * @return \Sm\Data\Entity\ContextualizedEntityProxy
     * @throws \Sm\Core\Context\Exception\InvalidContextException
     */
    public function proxyInContext(Context $context): EntitySchema {
        throw new InvalidContextException("Cannot proxy in any other contexts");
    }
    /**
     * @return array|mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function jsonSerialize() {
        $serialized_properties = [];
        $properties            = $this->getProperties();
        
        /**
         * @var \Sm\Data\Property\PropertySchema $property
         */
        foreach ($properties as $name => $property) {
            if ($property instanceof PropertySchematic)
                $serialized_properties[ $name ] = [
                    'smID'       => $property->getSmID(),
                    'datatypes'  => $property->getRawDatatypes(),
                    'isRequired' => $property->isRequired(),
                ];
            else if ($property instanceof Property) {
                $value = $property->resolve();
                if ($value instanceof Entity) $value = $value->proxyInContext($this->getContext());
                $serialized_properties[ $name ] = $value;
            }
        }
        
        return [
            'smID'       => $this->subject ? $this->subject->getSmID() : null,
            'properties' => $serialized_properties,
        ];
    }
}