<?php
/**
 * User: Sam Washington
 * Date: 3/13/17
 * Time: 9:04 PM
 */

namespace Sm\Core\Abstraction\Iterator;


trait IteratorTrait {
    public function current() {
        return $this->{$this->getRegistryName()}[ $this->key() ];
    }
    public function next() {
        next($this->{$this->getRegistryName()});
    }
    public function key() {
        return key($this->{$this->getRegistryName()});
    }
    public function valid() {
        return isset($this->{$this->getRegistryName()}[ $this->key() ]);
    }
    public function rewind() {
        reset($this->{$this->getRegistryName()});
    }
    
    abstract public function getRegistryName();
}