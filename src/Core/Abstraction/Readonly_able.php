<?php
/**
 * User: Sam Washington
 * Date: 7/6/17
 * Time: 8:41 PM
 */

namespace Sm\Core\Abstraction;


/**
 * Class ReadonlyTrait
 *
 * Trait to allow for reused Readonly functionality
 *
 * @package Sm\Core\Abstraction
 */
interface Readonly_able {
    /**
     * Mark the object as not being readonly. Then we can modify its contents as well
     *
     * @return bool
     */
    public function markNotReadonly();
    /**
     * Mark the object as being readonly. (we cannot modify its contents, only read them)
     *
     * @return bool
     */
    public function markReadonly();
    /**
     * Check to see if the object is readonly
     *
     * @return bool
     */
    public function isReadonly(): bool;
    /**
     * Set the readonly value on this object
     *
     * @param bool $readonly
     *
     * @return $this
     */
    public function setReadonly(bool $readonly);
}