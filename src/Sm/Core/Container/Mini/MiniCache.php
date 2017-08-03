<?php
/**
 * User: Sam Washington
 * Date: 4/13/17
 * Time: 10:23 AM
 */

namespace Sm\Core\Container\Mini;


use Sm\Core\Container\Cache\CacheInterface;
use Sm\Core\Container\Cache\CacheItem;
use Sm\Core\Resolvable\NullResolvable;
use Sm\Core\Util;

/**
 * Class MiniCache
 *
 * A class meant to make serve as a sort of a mini cache.
 * Mainly, this just allows us to see if a set of arguments has been called before,
 * as the Cache function takes a parameter that is meant to accomodate arrays or other non-string values
 *
 * @package Sm\Core\Container\Mini
 */
class MiniCache extends MiniContainer implements CacheInterface {
    protected $is_invalid = false;
    protected $cache_key;
    public static function init() {
        return new static;
    }
    /**
     * Has the Cache been started?
     *
     * @return bool
     */
    public function isCaching() {
        return isset($this->cache_key);
    }
    public function keyMatches($key) {
        return $this->cache_key === $key;
    }
    /**
     * Cache a result
     *
     * @param array|string|mixed $identity Whatever is going to be used to identify this object
     * @param mixed              $result   The Result that we want to cache
     *
     * @return $this
     */
    public function cache($identity, $result) {
        if (!isset($this->cache_key)) return $this;
        
        if (!isset($result)) return $this;
        
        
        $cache_key = $this->generateCacheIndex($identity);
        
        # Index the Resolve cache by the perceived handle of the function called by them.
        # Use the types of arguments to make it easier to determine if something is in a cache
        $this->registry[ $cache_key ] = $this->registry[ $cache_key ] ??[];
        
        # Add the CacheItem to the array of CacheItems
        $this->registry[ $cache_key ][] = CacheItem::init($result)->setIdentity($identity);
        
        return $this;
    }
    /**
     * Retrieve an item from the cache
     *
     * @param null $args The arguments that were used for the identity last time
     *
     * @return mixed|null
     */
    public function resolve($args = null) {
        if (!isset($this->cache_key)) return null;
        return $this->getItem($args, true)->resolve() ?? null;
    }
    public function canResolve($args) {
        if (!$this->isCaching()) return false;
        return parent::canResolve($args) && isset($this->cache_key);
    }
    /**
     * @param string $args
     * @param bool   $as_resolvable
     *
     * @return \Sm\Core\Resolvable\Resolvable|null
     */
    public function getItem($args, $as_resolvable = false) {
        $cache_index = $this->generateCacheIndex($args);
        $null        = $as_resolvable ? NullResolvable::init() : null;
        if (!isset($this->registry[ $cache_index ])) return $null;
        
        $index_registry = $this->registry[ $cache_index ];
        
        foreach ($index_registry as $index => $CacheItem) {
            /** @var \Sm\Core\Container\Cache\CacheItem $CacheItem */
            if ($CacheItem->isExpired()) {
                unset($index_registry[ $index ]);
                continue;
            }
            $args_equal = $CacheItem->compareIdentity($args);
            if ($args_equal) return $CacheItem;
        }
        
        return $null;
    }
    public function remove($args) {
        $item = $this->getItem($args);
        if ($item instanceof CacheItem) {
            $item->expire();
        }
        return $item;
    }
    /**
     * Only here because the MiniCache is also a registry. This is just used as an alias for cache
     *
     * @param mixed $identity
     * @param mixed $result
     *
     * @return \Sm\Core\Container\Mini\MiniCache
     */
    public function register($identity = null, $result = null) {
        return $this->cache(...func_get_args());
    }
    public function start($key = '-') {
        if (isset($this->cache_key)) return $this;
        $this->cache_key = $key;
        return $this;
    }
    public function end($key = '-') {
        if ($key !== $this->cache_key) {
            throw new InvalidCacheKeyException("Cannot end Cache - invalid key.");
        }
        $this->clear($key);
        $this->cache_key = null;
        return $this;
    }
    /**
     * Empty out the cache
     *
     *
     *
     * @param string $key The "key" that we are using to control access to the cache.
     *
     * @return $this
     * @throws \Sm\Core\Container\Mini\InvalidCacheKeyException
     */
    public function clear($key = '-') {
        if ($key !== $this->cache_key) {
            throw new InvalidCacheKeyException("Cannot clear Cache - invalid key.");
        }
        $this->registry = [];
        return $this;
    }
    public static function begin($key = '-') {
        $instance = static::init()->start($key);
        return $instance;
    }
    /**
     * Generate the Index at which the result of Container::resolve will be held for a set of arguments
     *
     * @param $result
     *
     * @return string
     */
    private function generateCacheIndex($result) {
        return Util::getShapeOfItem($result);
    }
}