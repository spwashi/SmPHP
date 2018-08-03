<?php


namespace Sm\Data\Model\Context;


use Sm\Core\Context\Proxy\StandardContextualizedProxy;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelInstance;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Model\Resolvable\RawModelPropertyResolvable;
use Sm\Data\Model\t_PropertyContainer;
use Sm\Data\Property\Context\PropertyContainerProxy;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyContainerInstance;


/**
 * Represents a Model as it would be accessed in a specific context
 *
 * @property-read PropertyContainer $properties
 */
class ContextualizedModelProxy extends StandardContextualizedProxy implements ModelInstance {
    /** @var Model */
    protected $subject;
    protected $search_properties;

    #
    ##  Generic Getters/Setters
    public function __get($name) {
        switch ($name) {
            case 'properties':
                return $this->getProperties();
            default:
                return $this->subject->__get($name);
        }
    }
    public function set($name, $value = null) {

        # Allow us to set iterables
        if (is_iterable($name)) {
            if (isset($value)) throw new InvalidArgumentException("Not sure what to do with iterable and Value");
            foreach ($name as $k => $value) $this->set($k, $value);
            return $this;
        }

        # if we are searching for this Model, we can set the search properties
        if ($this->context instanceof ModelSearchContext) {
            $propertyContainer = $this->initSearchPropertyContainer();
            $realProperty      = $this->subject->properties->$name;

            /** @var Property $realProperty */
            if (!$realProperty) throw new InvalidArgumentException("Cannot search for nonexistent property (" . $name . ")");
            $effectiveSchematic = $realProperty->getEffectiveSchematic();

            $searchProperty = $propertyContainer->resolve($name);
            if (is_null($searchProperty)) {
                $propertyContainer->registerSchematics([$name => clone $effectiveSchematic]);
                $searchProperty = $propertyContainer->resolve($name);
            }

            $searchProperty->setValue($value);

            return $this;
        }

        # if we are creating the Model, allow us to set values as RawProperties
        if ($this->context instanceof ModelCreationContext && $value instanceof RawModelPropertyResolvable) {
            $this->properties->set($name, $value, PropertyContainerProxy::SET_MODE__RAW, false);
            return $this;
        }

        throw new ReadonlyPropertyException("Cannot set Model Properties from a Proxy");
    }

    #
    ##  Getters and Setters
    public function getDataSource() { return false; }
    public function getModel(): Model {
        return $this->subject;
    }
    public function getName() { return $this->subject->getName(); }
    public function validate() {
        return $this->subject->validate($this->context);
    }
    public function getProperties($property_names = []): PropertyContainerInstance {
        $context = $this->getContext();

        # Return the search properties
        if ($context instanceof ModelSearchContext) {
            $search_properties = $this->search_properties ?? $this->initSearchPropertyContainer();
            return PropertyContainerProxy::init($search_properties, $context);
        }

        $propertyContainerProxy = $this->subject->properties->proxy($context);

        return $propertyContainerProxy->getProperties($property_names);
    }
    public function getSmID(): ?string { return $this->subject->getSmID(); }

    #
    ##  Private initialization
    private function initSearchPropertyContainer(): PropertyContainer {
        if (!isset($this->search_properties)) {
            $this->search_properties = new PropertyContainer;
            $this->search_properties->register($this->subject->getProperties()->getAll());
        }
        $this->search_properties->set($this->subject->getProperties()->getAll());
        return $this->search_properties;
    }
}