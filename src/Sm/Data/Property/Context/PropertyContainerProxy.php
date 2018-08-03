<?php


namespace Sm\Data\Property\Context;


use Sm\Core\Context\Context;
use Sm\Core\Context\Proxy\ContextualizedProxy;
use Sm\Core\Context\Proxy\StandardContextualizedProxy;
use Sm\Core\Exception\Exception;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Proxy\Proxy;
use Sm\Data\Model\Context\ModelCreationContext;
use Sm\Data\Model\Resolvable\RawModelPropertyResolvable;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyContainerInstance;
use Sm\Data\Property\PropertyInstance;
use Sm\Data\Property\PropertySchema;

/**
 * Represents a PropertyContainer that exists in a specific context
 */
class PropertyContainerProxy extends StandardContextualizedProxy implements ContextualizedProxy, PropertyContainerInstance {
    /** @var PropertyContainer */
    protected $subject;
    protected $context;
    const SET_MODE__RAW = 'set_mode__raw';

    public function __construct(PropertyContainer $subject, Context $context = null) {
        parent::__construct($subject, $context);
    }
    public static function init($subject = null, Context $context = null) {
        if (!isset($subject)) throw new InvalidArgumentException("Expected to proxy a PropertyContainer");
        return new static($subject, $context);
    }

    public function resolve($name = null): ?PropertyInstance {
        $property = $this->subject->resolve($name);
        if (!isset($property)) return null;
        $proxy = $this->createPropertyProxy($property);
        return $proxy;
    }
    public function getAll() {
        return $this->subject->getAll();
    }
    public function __set($name, $value) {
        return $this->subject->set($name, $value);
    }
    public function __get($name) {
        return $this->resolve($name);
    }
    public function set($name, $value = null, $set_mode = false) {
        if ($set_mode === static::SET_MODE__RAW) {
            $this->subject->set($name, $value, $set_mode);
            return $this;
        }
        throw new Exception("Cannot modify properties via default Container Proxy");

    }


    public function getContext(): Context {
        return $this->context;
    }
    public function setContext(Context $context) {
        $this->context = $context;
        return $this;
    }
    public function getChanged(): array {
        $property_array = $this->subject->getChanged();
        $this->convertToProxyArray($property_array);
        return $property_array;
    }
    public function getProperties($search = []): PropertyContainerInstance {
        $property_array    = $this->subject->getProperties($search)->getAll();
        $propertyContainer = PropertyContainer::init($property_array);
        return PropertyContainerProxy::init($propertyContainer, $this->context);
    }

    public function jsonSerialize() {
        return $this->getAll();
    }

    private function &convertToProxyArray(array &$property_array): array {
        foreach ($property_array as &$item) $item = new PropertyProxy($item, $this->context);
        return $property_array;
    }
    /**
     * @param $property
     * @return PropertyProxy
     */
    protected function createPropertyProxy($property): PropertyProxy {
        $proxy = new PropertyProxy($property, $this->context);
        return $proxy;
    }
}