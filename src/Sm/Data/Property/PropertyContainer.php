<?php


namespace Sm\Data\Property;

use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Property\Context\PropertyContainerProxy;
use Sm\Data\Property\Exception\NonexistentPropertyException;
use Sm\Data\Type\Undefined_;

/**
 * Class PropertyContainer
 */
class PropertyContainer extends PropertySchemaContainer implements PropertyInstantiator, PropertyContainerInstance {
	CONST SET_MODE__DEFAULT = false;
	CONST SET_MODE__SILENT  = true;
	/** @var PropertyInstantiator */
	protected $propertyInstantiator;

	#
	##  Constructors/Initialization
	public static function init($properties = []) { return (new static)->register($properties); }

	public function proxy(Context $context = null): PropertyContainerProxy {
		return PropertyContainerProxy::init($this, $context);
	}
	public function instantiate($schematic = null): Property {
		if (!isset($this->propertyInstantiator)) return new Property;
		return $this->propertyInstantiator->instantiate($schematic);
	}
	public function registerSchematics(iterable $properties) {
		$property_array = [];

		## Iterate over the schematic properties and register those on the PropertyContainer
		foreach ($properties as $index => $schematic) {
			$propertySchematic = null;

			if ($schematic instanceof Property) {
				# clone properties
				##  todo consider the implications
				$propertySchematic = clone $schematic;
			} else {
				# instantiate from everything else
				$propertySchematic = $this->instantiate($schematic);
			}

			$property_array[$index] = $propertySchematic;
		}

		# register the properties
		$this->register($property_array);
	}

	#
	##  Getters and Setters
	public function __set($name, $value) {
		if ($value instanceof PropertyInstance) $value = $value->getValue();
		$this->set($name, $value, true);
	}
	public function __get($name) {
		return $this->resolve($name);
	}
	public function set($name, $value = null, $silent = PropertyContainer::SET_MODE__DEFAULT) {
		if (is_array($name) && isset($value)) {
			throw new UnimplementedError("Not sure what to do with a name and value");
		}

		if ($name instanceof PropertyContainer) {
			$name = $name->getAll();
		}

		if (is_array($name)) {
			foreach ($name as $key => $val) $this->set($key, $val, $silent);

			return $this;
		}


		if ($value instanceof Property) $value = $value->value;

		/** @var  $property */
		$property = $this->resolve($name);

		if (!isset($property)) {

			# ignore errors
			if (PropertyContainer::SET_MODE__SILENT === $silent) return $this;

			throw new NonexistentPropertyException("Cannot set the value of a Property that doesn't exist");
		}

		$property->value = $value;

		return $this;
	}
	public function getChanged(): array {
		$changed_properties = [];

		/** @var \Sm\Data\Property\Property $property */
		foreach ($this->getAll() as $propertyName => $property) {
			if ($property->value instanceof Undefined_) continue;

			if ($property->valueHistory->count()) {
				$changed_properties[$propertyName] = $property;
			}
		}

		return $changed_properties;
	}
	public function setPropertyInstantiator(PropertyInstantiator $propertyInstantiator): PropertyContainer {
		$this->propertyInstantiator = $propertyInstantiator;
		return $this;
	}
	public function resolve($name = null): ?PropertyInstance {
		return parent::resolve($name);
	}

	#
	##  Internal Validation
	protected function checkRegistrantIsCorrectType($registrant): void {
		if (!($registrant instanceof Property)) {
			throw new InvalidArgumentException("Can only add Properties to the PropertyContainer (" . \Sm\Core\Util::getShape($registrant) . ' given)');
		}
	}
	public function getProperties($search = []): PropertyContainerInstance {
		if (empty($search)) {
			return PropertyContainer::init($this->getAll());
		}

		$end_properties = [];
		$allProperties  = $this->getAll();

		foreach ($allProperties as $name => $property) {
			if (!in_array($name, $search)) continue;
			$end_properties[$name] = $property;
		}

		return PropertyContainer::init($end_properties);
	}
}