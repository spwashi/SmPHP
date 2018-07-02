<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:30 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Abstraction\Readonly_able;
use Sm\Core\Abstraction\ReadonlyTrait;
use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\Monitor;
use Sm\Core\Resolvable\AbstractResolvable;
use Sm\Core\Resolvable\NativeResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Resolvable\ResolvableFactory;
use Sm\Core\Schema\Schematic;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntity;
use Sm\Core\SmEntity\Traits\Is_StdSchematicizedSmEntityTrait;
use Sm\Core\SmEntity\Traits\Is_StdSmEntityTrait;
use Sm\Core\Util;
use Sm\Data\Evaluation\Validation\Validatable;
use Sm\Data\Evaluation\Validation\ValidationResult;
use Sm\Data\Property\Event\PropertyValueChange;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\Validation\PropertyValidationResult;
use Sm\Data\Type\Datatype;
use Sm\Data\Type\Exception\CannotCastException;
use Sm\Data\Type\String_;
use Sm\Data\Type\Undefined_;

/**
 * Class Property
 *
 * Represents a property held by an Entity or Model
 *
 * @mixin ReadonlyTrait
 *
 * @package Sm\Data\Property
 *
 * @property-read \Sm\Core\Internal\Monitor\Monitor $valueHistory              A History of this Property & its values
 * @property-read string                            $object_id
 * @property-read array                             $potential_types
 * @property-read mixed                             $default_value             The Resolvable that holds the value of the Variable_
 * @property string                                 $name                      The name of the Variable_
 * @property mixed                                  $value                     The resolved value of this Variable_'s subject
 * @property Resolvable                             $raw_value                 The raw, unresolved Resolvable that this Variable_ holds a reference to
 *
 */
class Property extends AbstractResolvable implements Readonly_able,
                                                     PropertySchema,
                                                     Schematicized,
                                                     SmEntity,
                                                     Validatable,
                                                     \JsonSerializable {
	/** @var  Resolvable $subject */
	protected $subject;
	/** @var  Resolvable $_default */
	protected $_default;
	protected $_potential_types = [];
	protected $name;

	use ReadonlyTrait;
	use PropertyTrait;
	use Is_StdSmEntityTrait;
	use Is_StdSchematicizedSmEntityTrait {
		fromSchematic as protected _fromSchematic_std;
	}
	/** @var  \Sm\Core\Internal\Monitor\Monitor $valueHistory */
	protected $valueHistory;
	/** @var $valueIsNotDefault */
	protected $valueIsNotDefault;
	/** @var ReferenceDescriptorSchematic $referenceDescriptor */
	protected $referenceDescriptor;
	/** @var bool $doStrictResolve Should we throw an error when we try to resolve datatypes we don't "know" about? */
	protected $doStrictResolve = false;

	public function __construct($name = null) {
		$this->valueHistory = new Monitor;
		if (isset($name)) $this->name = $name;
		parent::__construct(Undefined_::init());
	}
	/**
	 * Create a Variable
	 *
	 * @param string|null $name The name of the Variable
	 *
	 * @return static
	 */
	public static function init($name = null) {
		$inst       = new static;
		$inst->name = $name;
		return $inst;
	}
	#
	##   Getters and Setters
	public function __get($name) {
		if ($name === 'object_id') return $this->getObjectId();
		if ($name === 'potential_types') return $this->getPotentialTypes();
		if ($name === 'valueHistory') return $this->getValueHistory();

		if ($name === 'name') {
			return $this->name;
		}
		if ($name === 'value') {
			return $this->getValue();
		}
		if ($name === 'raw_value') {
			return $this->subject;
		}
		return null;
	}
	/**
	 * @param $name
	 * @param $value
	 *
	 * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
	 */
	public function __set($name, $value) {
		if ($this->isReadonly()) throw new ReadonlyPropertyException("Cannot modify a readonly property");
		switch ($name) {
			case 'name':
				$this->name = $value;
				break;
			case 'value':
				$this->setValue($value);
				break;
		}

	}
	public function getReferenceDescriptor(): ?ReferenceDescriptorSchematic {
		return $this->referenceDescriptor;
	}
	public function setReferenceDescriptor(ReferenceDescriptorSchematic $referenceDescriptor): Property {
		$this->referenceDescriptor = $referenceDescriptor;
		return $this;
	}

	/**
	 * @param $subject
	 *
	 * @return $this
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 */
	public function setSubject($subject) {

		# --

		# Don't set from schematics
		if ($subject instanceof Schematic) return $this;

		$previous_value = $this->subject;

		if ($subject instanceof Property) $subject = $subject->value;

		if (!($subject instanceof Undefined_)) $this->valueIsNotDefault = true;

		# --

		# Only deal with Resolvables
		$subject = (new ResolvableFactory)->resolve($subject);
		$this->checkCanSetValue($subject);

		parent::setSubject($subject);

		# --

		$new_value = $this->getSubject();

		if ($previous_value instanceof NativeResolvable && get_class($previous_value) === get_class($new_value)) {
			$previous = $previous_value->resolve();
			$new      = $new_value->resolve();
		} else {
			$new      = $new_value;
			$previous = $previous_value;
		}

		if ($new !== $previous) {
			$this->markValueChange($new_value, $previous_value);
		}


		return $this;
	}
	/**
	 * Get a History of the values held by this Property (in this session?)
	 *
	 * @return \Sm\Core\Internal\Monitor\Monitor
	 */
	public function getValueHistory(): Monitor {
		return $this->valueHistory;
	}
	public function resetValueHistory() {
		$this->valueHistory->clear();
		return $this;
	}
	/**
	 * Set the Default Value
	 *
	 * @param Resolvable|mixed $default
	 *
	 * @return \Sm\Data\Property\Property
	 */
	public function setDefault(Resolvable $default): Property {
		$this->_default = $default;
		return $this;
	}
	/**
	 * Check to see if we are allowed to assign a value to a variable
	 *
	 * @param $subject
	 *
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 */
	protected function checkCanSetValue($subject) {
		# If we haven't given permission to set a Resolvable of this type, don't
		$potential_types = $this->_potential_types;
		if (!Util::isOneOfListedTypes($subject, $potential_types)) {
			throw new InvalidArgumentException("Cannot set subject to be this value");
		}
	}

	#
	## Manage property value
	/**
	 * Get an array of the potential types that this Variable can be
	 *
	 * @return array
	 */
	public function getPotentialTypes(): array {
		return $this->_potential_types;
	}
	/**
	 * Set an array of the potential types that a the value can be
	 *
	 * @param $_potential_types
	 *
	 * @return $this
	 */
	public function setPotentialTypes(string ...$_potential_types) {
		$this->_potential_types = $_potential_types;
		return $this;
	}

	/**
	 * @param $value
	 * @return $this|Property
	 */
	public function setValue($value) {
		return $this->setSubject($value);
	}
	public function getValue() {
		return $this->resolve();
	}
	/**
	 * This returns a resolvable that we could use in the future to get the actual value of the Property
	 * For things like serialization or saving, we might want ot use this to "lazily" determine the value of
	 *
	 * @return null|\Sm\Core\Resolvable\Resolvable
	 */
	protected function getResolvableValue(): ?Resolvable {
		if ($this->subject instanceof Undefined_) {
			return $this->subject;
		}

		$primaryDatatype = $this->getPrimaryDatatype();

		if ($primaryDatatype instanceof Datatype) {
			$primaryDatatype->setSubject($this->subject);
			return $primaryDatatype;
		}

		return $this->subject;
	}
	/**
	 * Return the Value of this subject or null if the subject doesn't exist
	 *
	 *
	 * @return Resolvable
	 * @throws \Sm\Core\Exception\UnimplementedError
	 */
	public function resolve() {
		$resolvable = $this->getResolvableValue();

		if ($this->doStrictResolve && !($resolvable instanceof Datatype)) {
			throw new UnimplementedError("Cannot resolve value for " . Util::getShape($resolvable));
		}

		$resolved = $resolvable instanceof Undefined_ ? $resolvable : $resolvable->resolve();

		return $resolved;
	}
	public function getPrimaryDatatype() {
		return $this->getDatatypes()[0] ?? null;
	}
	public function isValueNotDefault() {
		return $this->valueIsNotDefault;
	}
	public function validate(Context $context = null): ?ValidationResult {

		try {

			$resolved_value = $this->resolve();
			$datatype       = $this->getPrimaryDatatype();

			/** @var \Sm\Data\Entity\Property\EntityPropertySchematic $schematic */
			$schematic = $this->getEffectiveSchematic();
			$maxLength = $schematic ? $schematic->getLength() : null;

			if (isset($maxLength) && $datatype instanceof String_ && strlen($resolved_value) > $maxLength) {
				return $this->getValidationResult(false, 'Too long - can only be ' . $maxLength . ' characters');
			}

		} catch (CannotCastException|\Exception $e) {
			return $this->getValidationResult(false, $e->getMessage());
		}

		return null;
	}
	protected function getValidationResult($success = false, $error = null): PropertyValidationResult { return new PropertyValidationResult(...func_get_args()); }


	#
	##  Initialization
	public function fromSchematic($schematic) {
		/** @var \Sm\Data\Property\PropertySchematic $schematic */
		$this->_fromSchematic_std($schematic);

		$rawDataTypes = $this->getRawDataTypes();
		$name         = $this->getName() ?? ($schematic ? $schematic->getName() : null);
		if ($schematic instanceof Property) {
			$this->setValue($schematic->raw_value);
		}
		$referenceDescriptor = $this->getReferenceDescriptor() ?? ($schematic ? $schematic->getReferenceDescriptor() : null);
		$datatypes           = count($rawDataTypes) ? $rawDataTypes : ($schematic ? $schematic->getRawDataTypes() : null);

		if (isset($name)) $this->setName($name);
		if (isset($referenceDescriptor)) $this->setReferenceDescriptor($referenceDescriptor);
		if (isset($datatypes)) $this->setDatatypes($datatypes);

		return $this;
	}
	/**
	 * @param $schematic
	 *
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 */
	protected function checkCanUseSchematic($schematic) {
		if (!($schematic instanceof PropertySchema)) {
			throw new InvalidArgumentException("Can only initialize Properties using PropertySchematics");
		}
	}

	#
	##  Debugging/Serialization
	public function jsonSerialize() {
		if ($this->isValueNotDefault()) {
			return $this->resolve();
		}
		return null;
	}
	public function __debugInfo() {
		return ['subject' => $this->subject instanceof Undefined_ ? '~UNDEFINED~' : $this->subject];
	}
	public function setDoStrictResolve(bool $doStrictResolve) {
		$this->doStrictResolve = $doStrictResolve;
		return $this;
	}
	/**
	 * @param $new_value
	 * @param $previous_value
	 */
	protected function markValueChange($new_value, $previous_value): void {
		$this->valueHistory->append(PropertyValueChange::init($this, $new_value, $previous_value));
	}
}