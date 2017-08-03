<?php
/**
 * User: Sam Washington
 * Date: 2/19/17
 * Time: 12:50 AM
 */

namespace Sm\Core\Factory;


use Sm\Core\Container\Container;
use Sm\Core\Resolvable\Error\UnresolvableException;

/**
 * Class FactoryContainer
 *
 * Class that contains all of the Factories that a given operation might use.
 *
 * @package Sm\Core\Factory
 */
class FactoryContainer extends Container {
    public function register($name = null, $registrand = null, $overwrite = false) {
        # This class stores factories as an array of arrays
        $this->registry[ $name ]   = !$overwrite ? $this->registry[ $name ] ??[] : [];
        $this->registry[ $name ][] = $registrand;
        return $this;
    }
    public function resolve($name = null) {
        /** @var Factory[] $Factories */
        $Factories      = $this->_search_registry_for_name($name);
        $_factory_count = count($Factories);
        if ($_factory_count === 1) {
            return $Factories[0];
        } else if ($_factory_count < 2 && class_exists($name)) {
            $_class = new $name;
            if ($_class instanceof StandardFactory) {
                if (!$_factory_count) {
                    $this->register($name, $_class);
                }
                return $_class;
            }
        }
    
        throw new UnresolvableException("Cannot resolve Factory");
    }
    /**
     * Function that allows us to take advantage of an assumed distinction Factory names.
     * This function allows us to refer to a class with or without its namespace as long as
     * it's been registered here already
     *
     * @param $name
     *
     * @return array
     */
    private function _search_registry_for_name($name) {
        if (isset($this->registry[ $name ])) {
            return $this->registry[ $name ];
        } else if (class_exists($name)) {
            return [];
        }
        
        foreach ($this->registry as $index => $item) {
            $exp = explode('\\', $index);
            end($exp);
            $namespaceless_classname = $exp[ key($exp) ];
            if ($namespaceless_classname === $name) {
                return $item;
            }
        }
        
        return [];
    }
}