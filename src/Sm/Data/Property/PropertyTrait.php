<?php


namespace Sm\Data\Property;


use Sm\Data\Type\DatatypeFactory;

trait PropertyTrait {
    protected $_datatypeFactory;
    protected $_datatypes = [];
    
    public function getDatatypes(): array {
        return array_map(function ($item) {
            return $this->_getDatatypeFactory()->resolve($item);
        }, $this->_getDatatypes());
    }
    public function setDatatypes($datatypes) {
        if (!is_array($datatypes)) $datatypes = [ $datatypes ];
        $this->_setDatatypes($datatypes);
        return $this;
    }
    protected function setDatatypeFactory(DatatypeFactory $datatypeFactory = null) {
        $datatypeFactory = $datatypeFactory ?? $this->_getDatatypeFactory() ?? new DatatypeFactory;
        $this->_setDatatypeFactory($datatypeFactory);
        return $this;
    }
    
    protected function _getDatatypes(): array { return $this->_datatypes; }
    protected function _setDatatypes(array $datatypes) { $this->_datatypes = $datatypes; }
    protected function _getDatatypeFactory(): ?DatatypeFactory { return $this->_datatypeFactory; }
    protected function _setDatatypeFactory(DatatypeFactory $datatypeFactory) { $this->_datatypeFactory = $datatypeFactory; }
}