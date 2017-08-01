<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:07 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Core\Resolvable\Error\UnresolvableException;

/**
 * Class ArrayResolvable
 *
 * Resolvable that references arrays, or ultimately resolves to a array
 *
 * @package Sm\Core\Resolvable
 */
class ArrayResolvable extends NativeResolvable implements \JsonSerializable {
    public function setSubject($subject = null) {
        if (!is_array($subject)) {
            throw new UnresolvableException("Not sure how to resolve subjects that aren't arrays");
        }
        return parent::setSubject($subject);
    }
    /**
     * @param null $_
     *
     * @return array
     */
    public function resolve($_ = null) {
        return parent::resolve() ?? [];
    }
    
    function jsonSerialize() {
        return $this->subject;
    }
}