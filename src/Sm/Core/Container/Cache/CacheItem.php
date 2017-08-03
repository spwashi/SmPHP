<?php
/**
 * User: Sam Washington
 * Date: 4/13/17
 * Time: 10:15 AM
 */

namespace Sm\Core\Container\Cache;


use Sm\Core\Resolvable\NativeResolvable;

class CacheItem extends NativeResolvable {
    /** @var  string|array|mixed $identity Something that we can use to determine if this Cache item matches another */
    protected $identity;
    protected $marked_expired;
    /**
     * Check to see if an item matches this Cache item
     *
     * @param $item
     *
     * @return bool
     */
    public function compareIdentity($item) {
        if ($this->isExpired()) return false;
        $identity = $this->identity;
        return $item === $identity;
    }
    /**
     * @param array $identity
     *
     * @return \Sm\Core\Container\Cache\CacheItem
     */
    public function setIdentity($identity) {
        $this->identity = $identity;
        return $this;
    }
    public function expire() {
        $this->marked_expired = true;
        return $this;
    }
    public function isExpired() {
        return $this->marked_expired;
    }
}