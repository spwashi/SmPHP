<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:36 AM
 */

namespace Sm\Core\Factory;


use Sm\Core\Container\AbstractContainer;
use Sm\Core\Exception\ClassNotFoundException;
use Sm\Core\Exception\RecursiveError;
use Sm\Core\Factory\Exception\FactoryCannotBuildException;
use Sm\Core\Factory\Exception\WrongFactoryException;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\NativeResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Util;

/**
 * Class AbstractFactory
 *
 * Generic implementation of Factory Interface
 *
 * @package Sm\Core\Factory
 */
class StandardFactory extends AbstractContainer implements Factory {
    /** Mode of creating factories: Create classes that aren't registered (as long as it's okay to) */
    const MODE_DO_CREATE_MISSING = 'do_create_missing';
    
    /** @var  \Sm\Core\Container\Mini\MiniCache $cache */
    protected $cache;
    /** @var bool $do_shift_args_for_anonymous If false, we resolve each function with the name of the entity being resolved */
    protected $do_shift_args_for_anonymous = true;
    /** @var Resolvable[] */
    protected $registry = [];
    /** @var array $class_registry */
    protected $class_registry = [];
    /** @var bool If there is a class that isn't registered in the factory (and doesn't have ancestor that is), should we create it anyways? */
    protected $do_create_missing = true;
    /** @var array An array of the parents we've already gone through */
    protected $searched_parents = [];
    
    /**
     * Set the Mode of Creating Factories
     *
     * @param string $mode
     *
     * @return $this
     */
    public function setCreationMode($mode = StandardFactory::MODE_DO_CREATE_MISSING) {
        $this->do_create_missing = $mode === StandardFactory::MODE_DO_CREATE_MISSING;
        return $this;
    }
    public function resolve($name = null) {
        return $this->build(...func_get_args());
    }
    
    public function build() {
        $args   = func_get_args();
        $result = $this->Cache->resolve($args);
        
        # If we've already built the item and have it cached, return it
        if (isset($result)) return $result;
        
        
        # Try to build the item
        $result = $this->attempt_build(...$args);
        
        
        # Cache the result if we've decided that's necessary
        $this->Cache->cache($args, $result);
        return $result;
    }
    /**
     * Register a method to use to build this factory
     *
     * @param null $name
     *
     * @param      $registrant
     *
     * @return $this
     */
    public function register($name = null, $registrant = null) {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->register(is_numeric($key) ? null : $key, $value);
            }
            return $this;
        } else {
            $registrant = $this->standardizeRegistrant($registrant);
            
            
            # If the "name" is an object, just use the classname
            if (is_object($name)) {
                $name = get_class($name);
            }
            if (is_string($name)) {
                $this->class_registry[ $name ] = $registrant;
            }
            
            # register functions that don't have a name
            #  or FunctionResolvables that don't have an
            else if (!$name) {
                array_unshift($this->registry, $registrant);
            }
        }
        return $this;
    }
    
    /**
     * Try to build something without returning a default
     *
     * @param $item
     *
     * @return mixed
     * @throws \Sm\Core\Factory\Exception\FactoryCannotBuildException If we can't build the item
     */
    protected function attempt_build($item = null) {
        $original_args = $args = func_get_args();
        /** @var string $class_name */
        $class_name         = is_object($item) ? get_class($item) : $item;
        $previous_exception = null;
        if ($this->shouldBuildClassInstance($class_name)) {
            try {
                array_shift($args);
                # If the original class exists or we found a match, create the class
                $result = $this->buildClassInstance($class_name, $args);
                return $result;
            } catch (ClassNotFoundException $e) {
                $previous_exception = $e;
            } finally {
                $this->searched_parents = [];
            }
        }
        
        
        # Iterate through the other registry to see if there is some sort of different check
        #  being done
        foreach ($this->registry as $index => $method) {
            /*** @var Resolvable $method */
            $result = $method->resolve(...($this->do_shift_args_for_anonymous ? $args : $original_args));
            if ($result) return $result;
        }
        
        #
        # Could not find instance
        $arg_shape = self::__getArgShape($args);
        throw new FactoryCannotBuildException("Cannot find a matching build method for " . $arg_shape, null, $previous_exception);
    }
    /**
     * Build a class relevant to this Factory
     *
     * @param string $class_name
     *
     * @param array  $args
     *
     * @return mixed|null
     * @throws \Sm\Core\Exception\ClassNotFoundException
     * @throws \Sm\Core\Exception\RecursiveError
     */
    protected function buildClassInstance(string $class_name, array $args = []) {
        # If there is a function to help us create the class, call that function with the original class name that we
        #  are trying to create
        $class_handler = Util::getItemByClassAncestry($class_name, $this->class_registry);
        $instance      = null;
        
        if (in_array($class_handler, $this->searched_parents)) throw new RecursiveError("Recursively calling class handler for {$class_name}");
        $this->searched_parents[] = $class_handler;
        
        
        # If we are resolving a function, return that result.
        # Otherwise, set the class handler to be whatever the classhandler resolves to
        if ($class_handler instanceof Resolvable) {
            $instance = $class_handler->resolve(...$args);
        }
        
        if (is_string($instance) && class_exists($instance)) {
            $class_name = $instance; # Assume we are dealing with a class name ... maybe do a check?
        }
        
        if (is_object($instance)) {
            # Check to see if we're allowed to interact with objects of this type
            $this->_checkCanInit($instance);
            return $instance;
        }
        
        if (!class_exists($class_name)) {
            throw new ClassNotFoundException("Class {$class_name} not found");
        }
        
        $this->_checkCanInit($class_name);
        $class = new $class_name(...$args);
        return $class;
    }
    /**
     * Are we allowed to create factories of this class type?
     *
     * @param string|object $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        return true;
    }
    /**
     * @param mixed $registrant Whatever is being registered
     *
     * @return null|\Sm\Core\Resolvable\Resolvable
     */
    protected function standardizeRegistrant($registrant): ?Resolvable {
        if (is_object($registrant) && (get_class($registrant) !== \Closure::class)) {
            $registrant = function () use ($registrant) {
                return clone $registrant;
            };
        }
        
        return is_callable($registrant) ? FunctionResolvable::init($registrant) : NativeResolvable::init($registrant);
    }
    private function _checkCanInit($class_name) {
        if (!$this->canCreateClass($class_name) || !$this->do_create_missing) {
            $type       = is_string($class_name) ? $class_name : Util::getShape($class_name);
            $self_class = static::class;
            throw new WrongFactoryException("{$self_class} not allowed to create class of type {$type}");
        }
    }
    /**
     * @param $class_name
     *
     * @return bool
     */
    private static function isProbablyClassname($class_name): bool {
        return is_string($class_name) && (strpos($class_name, '\\') !== false || class_exists($class_name));
    }
    /**
     * @param $class_name
     *
     * @return bool
     */
    protected function shouldBuildClassInstance($class_name): bool {
        return static::isProbablyClassname($class_name) ||
               (is_string($class_name)
                   ? $class_name
                   : ($class_name = gettype($class_name)) && isset($this->class_registry[ $class_name ]));
    }
    /**
     * @param $args
     *
     * @return string
     */
    private static function __getArgShape($args): string {
        $_args     = count($args) === 1 ? $args[0] : $args;
        $arg_shape =
            Util::canBeString($_args)
                ?
                $_args
                :
                (Util::getShape($_args));
        
        if (is_array($_args) || ($_args instanceof \JsonSerializable)) {
            $arg_shape .= ' - ' . json_encode($_args);
        }
        return $arg_shape ?? 'null';
    }
}