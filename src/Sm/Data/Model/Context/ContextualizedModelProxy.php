<?php


namespace Sm\Data\Model\Context;


use Sm\Core\Context\Proxy\StandardContextualizedProxy;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelInstance;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Model\t_PropertyContainer;
use Sm\Data\Property\Context\PropertyContainerProxy;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyContainerInstance;


/**
 * Represents a Model as it would be accessed in a specifc context
 *
 * @property-read PropertyContainer $properties
 */
class ContextualizedModelProxy extends StandardContextualizedProxy implements ModelInstance {
	/** @var Model */
	protected $subject;
	protected $search_properties;

	#
	##  Generic Getters/Setters
	public function __get($name) {
		switch ($name) {
			case 'properties':
				return $this->getProperties();
			default:
				return $this->subject->__get($name);
		}
	}
	public function set($name, $value = null) {

		# Allow us to set iterables
		if (is_iterable($name)) {
			if (isset($value)) throw new InvalidArgumentException("Not sure what to do with iterable and Value");
			foreach ($name as $k => $value) $this->set($k, $value);
			return $this;
		}

		# If the <context within which this Model was Proxied> is
		if ($this->context instanceof ModelSearchContext) {
			$propertyContainer = $this->initSearchPropertyContainer();
			$propertyContainer->register($name, $value);
			return $this;
		}

		throw new ReadonlyPropertyException("Cannot set Model Properties from a Proxy");
	}

	#
	##  Getters and Setters
	public function getDataSource() { return false; }
	public function getModel(): Model {
		return $this->subject;
	}
	public function getName() { return $this->subject->getName(); }
	public function validate() {
		return $this->subject->validate($this->context);
	}
	public function getProperties($property_names = []): PropertyContainerInstance {
		$context = $this->getContext();


		if ($context instanceof ModelSearchContext) {
			$search_properties = $this->search_properties ?? $this->initSearchPropertyContainer();

			return PropertyContainerProxy::init($search_properties, $context);
		}

		$propertyContainerProxy = $this->subject->properties->proxy($context);

		return $propertyContainerProxy->getProperties($property_names);
	}
	public function setProperties($properties) { throw new ReadonlyPropertyException("Cannot set all Model Properties through a Proxy"); }
	public function getSmID(): ?string { return $this->subject->getSmID(); }

	#
	##  Private initialization
	private function initSearchPropertyContainer(): PropertyContainer {
		if (!isset($this->search_properties)) {
			$this->search_properties = new PropertyContainer;
			$this->search_properties->register($this->subject->getProperties()->getAll());
		}
		return $this->search_properties;
	}
}