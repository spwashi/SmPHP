<?php


namespace Sm\Data\Property\Context;


use Sm\Core\Context\Context;
use Sm\Core\Context\Proxy\ContextualizedProxy;
use Sm\Core\Context\Proxy\StandardContextualizedProxy;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyInstance;
use Sm\Data\Property\PropertySchema;

class PropertyProxy extends StandardContextualizedProxy implements PropertyInstance {
    /** @var Property */
    protected $subject;
    protected $context;
    public function getName() {
        return $this->subject->getName();
    }
    public function setName(string $name) {
        return $this->subject->setName($name);
    }
    public function getDatatypes(): array {
        return $this->subject->getDatatypes();
    }
    public function setDatatypes($datatypes) {
        return $this->subject->setDatatypes($datatypes);
    }
    public function getSmID(): ?string {
        return $this->subject->getSmID();
    }
    public function getValue() {
        return $this->subject->getValue();
    }
    public function resolve() {
        return $this->subject->resolve();
    }
    public function __toString() {
        return $this->subject->__toString();
    }
}