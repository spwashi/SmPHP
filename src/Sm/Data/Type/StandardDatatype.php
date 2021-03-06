<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 8:46 PM
 */

namespace Sm\Data\Type;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\AbstractResolvable;
use Sm\Core\Resolvable\Exception\InvalidResolvableSubjectException;
use Sm\Core\Resolvable\ResolvableFactory;
use Sm\Core\SmEntity\Traits\Is_StdSchematicizedSmEntityTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntityTrait;
use Sm\Data\Type\Exception\CannotCastException;

abstract class StandardDatatype extends AbstractResolvable implements Datatype {
	use Is_StdSmEntityTrait;
	use Is_StdSchematicizedSmEntityTrait;
	/** @var  \Sm\Core\Resolvable\Resolvable $subject */
	protected $subject;

	public function setSubject($subject) {
		$this->subject = $subject;
		return $this;
	}
	public function checkCanUseSchematic($schematic = null) {
		if (isset($schematic)) throw new UnimplementedError("Cannot apply schematics to most datatypes");
	}
	/**
	 * @param null|mixed $_ ,..
	 *
	 * @return mixed
	 * @throws \Sm\Data\Type\Exception\CannotCastException
	 */
	public function resolve() {
		try {
			$subject = $this->subject;
			$subject = static::resolveType($subject);
			return $subject->resolve();
		} catch (InvalidResolvableSubjectException $exception) {
			throw new CannotCastException("Cannot set value -- invalid type", 0, $exception);
		}
	}
	function jsonSerialize() {
		$class = static::class;
		$expl  = explode('\\', $class);
		end($expl);
		return "[" . $expl[key($expl)] . "]";
	}
	/**
	 * Represent the subject internally in a particular way
	 *
	 * @param $subject
	 *
	 * @return \Sm\Core\Resolvable\Resolvable
	 */
	public static function resolveType($subject) {
		return ResolvableFactory::init()
		                        ->resolve($subject);
	}
	public function fromSchematic($schematic = null) {
		return $this;
	}
}