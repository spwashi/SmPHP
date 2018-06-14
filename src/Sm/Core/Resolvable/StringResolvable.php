<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:07 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Data\Type\Exception\CannotCastException;

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
	/**
	 * StringResolvable constructor.
	 *
	 * @param null $subject
	 *
	 * @throws \Sm\Data\Type\Exception\CannotCastException
	 */
	public function __construct($subject = null) {
		if (!static::itemCanBeString($subject)) {
			throw new CannotCastException("Cannot determine intended value");
		}
		parent::__construct($subject);
	}
	public function __debugInfo() {
		return ['value' => $this->subject ?? null];
	}
	public function __toString() {
		return $this->resolve();
	}
	public function resolve($_ = null) {
		if ($this->subject instanceof Resolvable) $subject = $this->subject->resolve();
		else $subject = $this->subject;

		return "{$subject}";
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
		return $var === null || is_scalar($var) || is_callable([$var, '__toString']);
	}
}