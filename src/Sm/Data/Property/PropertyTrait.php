<?php


namespace Sm\Data\Property;


use Sm\Core\Resolvable\Exception\UnresolvableException;
use Sm\Data\Type\DatatypeFactory;

trait PropertyTrait {
    protected $_datatypeFactory;
    protected $_datatypes = [];
    
    public function getRawDataTypes() {
        return $this->_datatypes;
    }
    public function getDatatypes(): array {
        $resolveDataType = function ($item) {
            $datatypeFactory = $this->getDatatypeFactory();
            if (!isset($datatypeFactory)) throw new UnresolvableException("Missing DataTypeFactory");
            return $datatypeFactory->resolve($item);
        };
        return array_map($resolveDataType, $this->getRawDataTypes());
    }
    public function setDatatypes($datatypes) {
        if (!is_array($datatypes)) $datatypes = [ $datatypes ];
        $this->_datatypes = $datatypes;
        return $this;
    }
    public function setDatatypeFactory(DatatypeFactory $datatypeFactory = null) {
        $datatypeFactory        = $datatypeFactory ?? $this->getDatatypeFactory() ?? new DatatypeFactory;
        $this->_datatypeFactory = $datatypeFactory;
        return $this;
    }
    
    protected function getDatatypeFactory(): ?DatatypeFactory { return $this->_datatypeFactory; }
}