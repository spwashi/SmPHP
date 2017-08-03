<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:07 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Core\Resolvable\Error\UnresolvableException;

/**
 * Class StringResolvable
 *
 * Resolvable that references strings, or ultimately resolves to a string
 *
 * @package Sm\Core\Resolvable
 */
class StringResolvable extends NativeResolvable implements \JsonSerializable {
    /** @var */
    protected $subject;
    public function __construct($subject = null) {
        if (!static::itemCanBeString($subject)) {
            throw new UnresolvableException("Could not resolve subject");
        }
        parent::__construct($subject);
    }
    public function __debugInfo() {
        return [ 'value' => $this->subject ?? null ];
    }
    public function __toString() {
        return $this->resolve();
    }
    public function resolve($_ = null) {
        return "$this->subject";
    }
    /**
     * JSON Serialization just returns the stringified version of this
     *
     * @return string
     */
    public function jsonSerialize() {
        return "$this";
    }
    /**
     * Function to determine whether something can be a string
     * ::UTIL::
     *
     * @param $var
     *
     * @return bool
     */
    protected static function itemCanBeString($var) {
        return $var === null || is_scalar($var) || is_callable([ $var, '__toString' ]);
    }
}