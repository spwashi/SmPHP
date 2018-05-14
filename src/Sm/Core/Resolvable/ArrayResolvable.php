<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:07 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Data\Type\Exception\CannotCastException;

/**
 * Class ArrayResolvable
 *
 * Resolvable that references arrays, or ultimately resolves to a array
 *
 * @package Sm\Core\Resolvable
 */
class ArrayResolvable extends NativeResolvable implements \JsonSerializable {
    /**
     * @param null $subject
     *
     * @return \Sm\Core\Resolvable\ArrayResolvable|\Sm\Core\Resolvable\NativeResolvable
     * @throws \Sm\Data\Type\Exception\CannotCastException
     */
    public function setSubject($subject = null) {
        if (!is_array($subject)) {
            throw new CannotCastException("Value should be an array");
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