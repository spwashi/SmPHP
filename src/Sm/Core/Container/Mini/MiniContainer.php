<?php
/**
 * User: Sam Washington
 * Date: 4/13/17
 * Time: 10:09 AM
 */

namespace Sm\Core\Container\Mini;

use Sm\Core\Abstraction\Iterator\IteratorTrait;
use Sm\Core\Abstraction\Registry;

/**
 * Class MiniContainer
 *
 * Not a full registry, but it kind of acts like one.
 *
 * @package Sm\Core\Container
 */
class MiniContainer implements Registry, \Iterator {
    use IteratorTrait;
    
    protected $registry = [];
    public static function init() { return new static; }
    /**
     * Remove an item from the Registry, return it
     *
     * @todo test
     *
     * @param $name
     *
     * @return mixed
     */
    public function remove($name) {
        if (!isset($this->registry[ $name ])) return null;
        
        $variable = $this->registry[ $name ];
        unset($this->registry[ $name ]);
        
        return $variable;
    }
    /**
     * Get the Key that we are going to iterate on
     *
     * @return null|string
     */
    public function getRegistryName() {
        return isset($this->registry) ? 'registry' : null;
    }
    /**
     * Function to return everything from the registry.
     *
     * @return array
     */
    public function getAll() {
        return $this->registry;
    }
    /**
     * @param string|mixed|null $name
     * @param mixed|null        $registrand
     *
     * @return $this
     */
    public function register($name = null, $registrand = null) {
        $this->addToRegistry($name, $registrand);
        return $this;
    }
    /**
     * Can we resolve what we're trying to?
     *
     * @param $name
     *
     * @return bool
     */
    public function canResolve($name) {
        return null !== ($this->getItem($name));
    }
    public function __get($name) {
        return $this->resolve($name);
    }
    public function __set($name, $value) {
        $this->register($name, $value);
    }
    public function resolve($name = null) {
        if (!is_scalar($name)) {
            return null;
        }
        
        return $this->registry[ $name ] ?? null;
    }
    public function count() {
        return count($this->registry);
    }
    protected function addToRegistry($name, $item) {
        $this->registry[ $name ] = $item;
        return $this;
    }
    /**
     * Get an item from the registry
     *
     * @param string $name The class or index of the item that we are looking for
     *
     * @return mixed
     */
    protected function getItem($name) {
        if (!is_string($name)) {
            return null;
        }
        return $this->registry[ $name ] ?? null;
    }
}