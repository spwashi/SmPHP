<?php
/**
 * User: Sam Washington
 * Date: 3/15/17
 * Time: 11:38 PM
 */

namespace Sm\Core\Abstraction;

/**
 * Class ReadonlyTrait
 *
 * Trait to allow for reused Readonly functionality
 *
 * @package Sm\Core\Abstraction
 */
trait ReadonlyTrait {
    /** @var bool Can we modify the contents of this object? */
    protected $readonly = false;
    
    /**
     * Mark the object as not being readonly. Then we can modify its contents as well
     *
     * @return bool
     */
    public function markNotReadonly() {
        $this->readonly = false;
        return true;
    }
    /**
     * Mark the object as being readonly. (we cannot modify its contents, only read them)
     *
     * @return bool
     */
    public function markReadonly() {
        $this->readonly = true;
        return true;
    }
    /**
     * Check to see if the object is readonly
     *
     * @return bool
     */
    public function isReadonly(): bool {
        return $this->readonly;
    }
    /**
     * Set the readonly value on this object
     *
     * @param bool $readonly
     *
     * @return $this
     */
    public function setReadonly(bool $readonly) {
        $this->readonly = $readonly;
        return $this;
    }
}