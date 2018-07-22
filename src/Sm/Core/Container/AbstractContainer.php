<?php
/**
 * User: Sam Washington
 * Date: 4/5/17
 * Time: 7:37 PM
 */

namespace Sm\Core\Container;


use Sm\Core\Container\Mini\MiniCache;
use Sm\Core\Container\Mini\MiniContainer;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Util;

/**
 * Class AbstractContainer
 *
 * @package Sm\Core\Container
 * @property-write $search_ancestry
 */
abstract class AbstractContainer extends MiniContainer {
    use HasMonitorTrait;
    const ITEM_NOT_RESOLVABLE = 'AbstractContainer::x|not_resolvable';
    
    /** @var  \Sm\Core\Container\Mini\MiniCache $cache */
    protected $cache;
    protected $registry = [];
    #todo figure this out
    protected $_registered_defaults = [];
    
    /** @var bool $do_search_ancestry Should we search the Class ancestry of the items we're looking for?
     *                                When we search for something of one class type, we might also want
     *                                to search for items of another class type */
    protected $do_search_ancestry = false;
    
    
    #  Constructors/Initializers
    public function __construct() {
        $this->cache = new MiniCache;
    }
    /**
     * @return \Sm\Core\Container\Container|static
     */
    public static function init() { return new static; }
    #-----------------------------------------------------------------------------------
    #  Public Methods
    #-----------------------------------------------------------------------------------
    public function __get($name) {
        if ($name === 'Cache') {
            return $this->cache;
        } else {
            return parent::__get($name);
        }
    }
    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        # al
        if ($name === 'do_search_ancestry') {
            $this->do_search_ancestry = boolval($value);
        }
        parent::__set($name, $value);
    }
    /**
     * Return a new instance of this class that inherits this registry
     *
     * @return static|$this
     */
    public function duplicate() {
        $Container                       = static::init();
        $registry                        = $this->cloneRegistry();
        $Container->_registered_defaults = $this->_registered_defaults;
    
        $Container->register($registry);
        return $Container;
    }
    /**
     * Inherit the contents of another Container
     *
     * @param \Sm\Core\Container\AbstractContainer $registry
     *
     * @return $this
     */
    public function inherit(AbstractContainer $registry) {
        $this->register($registry->cloneRegistry());
        return $this;
    }
    /**
     * Register default values that are only there until they are overwritten.
     * #todo why did you write this?
     *
     * @param      $name
     * @param null $registrant
     *
     * @return $this
     */
    public function registerDefaults($name, $registrant = null) {
        if (is_array($name)) {
            foreach ($name as $index => $item) {
                $this->registerDefaults($index, $item);
            }
            return $this;
        }
        
        if (($this->_registered_defaults[ $name ] ?? false) || !($this->canResolve($name))) {
            $this->register($name, $registrant);
            $this->_registered_defaults[ $name ] = true;
        }
        return $this;
    }
    /**
     * Register an item or an array of items (indexed by name) as being things that are going to get resolved by this Container container
     *
     * @param string|array                   $name       Could also be an associative array of whatever we are registering
     * @param Resolvable|callable|mixed|null $registrant Whatever is being registered. Null if we are registering an array
     *
     * @return $this
     */
    public function register($name = null, $registrant = null) {
        if (is_array($name)) {
            foreach ($name as $index => $item) $this->register($index, $item);
    
            return $this;
        }
    
        $registrant = $this->standardizeRegistrant($registrant);
        $this->__info_monitor__log('register', $name, $registrant);
        parent::register($name, $registrant);
        return $this;
    }
    /**
     * @param string $name The name of whatever we are going to resolve
     *
     * @return mixed|null
     */
    public function resolve($name = null) {
        $args = func_get_args();
        $item = parent::resolve($name);
        if (!($item instanceof Resolvable)) {
            $this->__info_monitor__log(AbstractContainer::ITEM_NOT_RESOLVABLE, $name, $args);
            return $item;
        }
        array_shift($args);
        return $this->getResolvedValue($item, $args);
    }
    
    #-----------------------------------------------------------------------------------
    #  Private/Protected methods
    #-----------------------------------------------------------------------------------
    /**
     * Duplicate the registry for the sake of inheritance
     *
     * @return array
     */
    protected function cloneRegistry() {
        $registry     = $this->registry;
        $new_registry = [];
        foreach ($registry as $identifier => $item) {
            if ($identifier) {
                if (is_object($item)) {
                    $new_registry[ $identifier ] = clone $item;
                } else {
                    $new_registry[ $identifier ] = $item;
                }
            }
        }
        return $new_registry;
    }
    /**
     * @param mixed $registrant Whatever is being registered
     *
     * @return null|Resolvable
     */
    abstract protected function standardizeRegistrant($registrant):? Resolvable;
    /**
     * Add something to the registry (meant to represent the actual action)
     *
     * @param string     $name
     *
     * @param Resolvable $item
     *
     * @return $this
     */
    protected function addToRegistry($name, $item) {
        parent::addToRegistry($name, $item);
    
        # If there was a default value provided for this, remove that default.
        # todo why is this a thing again?
        if ($this->_registered_defaults[ $name ] ?? false) {
            unset($this->_registered_defaults[ $name ]);
        }
    
        return $this;
    }
    /**
     * Get an item from the registry
     *
     * @param string $name The class or index of the item that we are looking for
     *
     * @return Resolvable|null
     */
    protected function getItem($name) {
        if (!is_string($name)) {
            return null;
        }
    
        # If we are allowed to
        if ($this->do_search_ancestry && class_exists($name)) {
            return Util::getItemByClassAncestry($name, $this->registry);
        }
        return parent::getItem($name);
    }
    /**
     * Resolve the final value of something in this class
     *
     * @param Resolvable $item
     * @param            $args
     *
     * @return mixed
     */
    protected function getResolvedValue(Resolvable $item, $args) {
        $result = $item->resolve(...$args);
        return $result;
    }
}