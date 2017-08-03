<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:09 PM
 *
 * @package Sm
 *
 */

namespace Sm\Core\Container;


use Sm\Core\Container\Mini\MiniCache;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Factory\Factory;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\NullResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Resolvable\ResolvableFactory;

/**
 * Class Container
 *
 * @package Sm\Core\Container
 *
 * @property \Sm\Core\Container\Mini\MiniCache $Cache
 *
 * @coupled \Sm\Core\Resolvable\Resolvable
 */
class Container extends AbstractContainer {
    /**
     * @var Factory
     */
    protected $ResolvableFactory = null;
    
    /** @var bool */
    protected $do_consume = false;
    /** @var  MiniCache $ConsumedItems */
    protected $ConsumedItems;
    protected $CheckedOutItems;
    
    /**
     * Container constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->ResolvableFactory = new ResolvableFactory;
        $this->CheckedOutItems   = MiniCache::begin();
    }
    
    #------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------
    /**
     * Determine how we are going to "consume" resources.
     * In other words, should we only resolve something once, and return null for everything else?
     * Should we only resolve once for one set of arguments?
     *
     * @param bool $do_consume
     *
     * @return $this
     */
    public function setConsumptionMode($do_consume = true) {
        $this->do_consume    = $do_consume;
        $this->ConsumedItems = $this->ConsumedItems ?? MiniCache::begin();
        return $this;
    }
    /**
     * Reset the list of items that we've "consumed" for a certain set of arguments
     *
     * @return $this
     */
    public function resetConsumedItems() {
        $this->ConsumedItems = MiniCache::begin();
        return $this;
    }
    
    /**
     * Mark a particular set of Arguments as "checked out".
     * This assumes that item identifiers - not items - are the things that we check out.
     *
     * Returns either a NullResolvable, or a Resolvable that will resolve a value once
     *
     * @param      $args
     *
     * @return NullResolvable|\Sm\Core\Container\ContainerItemResolverResolvable
     */
    public function checkout(...$args) {
        if ($this->isCheckedOut(...$args)) {
            return NullResolvable::init();
        }
        
        # The function calls this->resolve(...$args).
        /** @see Container::resolve() */
        /** @var FunctionResolvable Resolve the arguments $Resolver */
        $Resolver = FunctionResolvable::init()
                                      ->setSubject([ $this, 'resolve' ])
                                      ->setArguments(...$args);
        
        $Resolvable = ContainerItemResolverResolvable::init($Resolver);
        
        if (!$this->markCheckedOut($args, $Resolvable)) return NullResolvable::init();
        
        return $Resolvable;
    }
    /**
     * "Return" an item to the checkout.
     *
     * @param Resolvable $Resolver
     *
     * @return bool|null
     * @throws \Sm\Core\Exception\Exception
     */
    public function checkBackIn(Resolvable &$Resolver) {
        # Because the "checkout" function returns ContainerItemResolverResolvables, only accept those.
        #todo error
        if (!($Resolver instanceof ContainerItemResolverResolvable)) return null;
        
        # Get the FunctionResolvable to find it in the CheckedOutItems container
        $FnResolvable = $Resolver->getSubject();
        $args         = $FnResolvable instanceof FunctionResolvable ? $FnResolvable->getArguments() : null;
        
        # If there aren't any arguments, something's probably wrong
        if (!isset($args)) throw new InvalidArgumentException("Cannot identify checked out resource.");
        
        # Compare the arguments of the Resolver to see if they match what we expect.
        $checkout_key = $this->CheckedOutItems->resolve($args);
        if ($Resolver->getObjectId() !== $checkout_key) return false;
        
        # The Item should not be kept in the "checked out items"
        $this->CheckedOutItems->remove($args);
        
        # Void the Resolver
        $Resolver = null;
        
        # If we can't resolve whatever, return true on success
        return !$this->CheckedOutItems->canResolve($args);
    }
    /**
     * Check whether something has been checked out or not
     *
     * @param $args
     *
     * @return bool
     */
    public function isCheckedOut(...$args): bool {
        return $this->CheckedOutItems->canResolve($args);
    }
    /**
     * Retrieve an Item from the Container
     *
     * @param string|mixed $name The name of the item in this container
     *
     * @return mixed|null
     */
    public function resolve($name = null) {
        $args = func_get_args();
        array_shift($args);
        
        # If we've already resolved for this item and that matters, return
        if ($this->do_consume && $this->ConsumedItems->resolve($args)) {
            return null;
        }
        
        # Check the cache first for the result
        if (null !== ($result = $this->Cache->resolve($args))) {
            return $result;
        }
        
        # Try to resolve as usual
        $result = parent::resolve($name, ...$args);
        
        # Huh
        if (!isset($result)) {
            return null;
        }
        
        # If we want to keep track of what we've consumed, mark this one
        if ($this->do_consume) $this->markConsumed($args);
        
        # Cache the result if we've decided that's necessary (the cache was started)
        $this->Cache->cache($args, $result);
        
        
        return $result;
    }
    /**
     * Identify something as being "consumed", or no longer available in the Container.
     * Here, we only "Consume" an identifier, not a resource. Thus, go by the arguments as identifiers
     *
     * @param $args
     *
     * @return bool
     */
    protected function markConsumed($args) {
        $this->ConsumedItems->cache($args, true);
        return $this->ConsumedItems->canResolve($args);
    }
    /**
     * Identify a Resolvable as being Checked Out
     *
     * @param                                $args
     * @param \Sm\Core\Resolvable\Resolvable $Resolvable
     *
     * @return bool
     */
    protected function markCheckedOut($args, Resolvable $Resolvable) {
        # Add the Item using the Resolvable's object ID.
        # Items can only be used once per. The result of this is the object ID of the Resolvable
        $this->CheckedOutItems->cache($args, $Resolvable->getObjectId());
        return $this->CheckedOutItems->canResolve($args);
    }
    /**
     * @inheritdoc
     *
     * @param mixed $registrand
     *
     * @return null|\Sm\Core\Resolvable\FunctionResolvable|\Sm\Core\Resolvable\NativeResolvable|\Sm\Core\Resolvable\StringResolvable
     */
    protected function standardizeRegistrand($registrand):?Resolvable {
        return isset($this->ResolvableFactory) ? $this->ResolvableFactory->build($registrand) : null;
    }
}