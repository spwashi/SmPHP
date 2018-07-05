<?php


namespace Sm\Data\Entity\Context;


use ReflectionMethod;
use Sm\Core\Context\Context;
use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Proxy\ContextualizedProxy;
use Sm\Core\Context\Proxy\StandardContextualizedProxy;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Proxy\Proxy;
use Sm\Core\Resolvable\Exception\UnresolvableException;
use Sm\Core\Schema\Schematic;
use Sm\Data\Entity\Entity;
use Sm\Data\Entity\EntitySchema;
use Sm\Data\Entity\EntitySchematic;
use Sm\Data\Entity\Property\EntityPropertySchematic;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertySchemaContainer;
use Sm\Data\Property\PropertySchematic;


class ContextualizedEntityProxy extends StandardContextualizedProxy implements Proxy, ContextualizedProxy, \JsonSerializable, EntitySchema {
	/** @var \Sm\Data\Entity\EntitySchema */
	protected $subject;
	/** @var \Sm\Data\Entity\Context\EntityContext $context */
	protected $context;

	/**
	 * ContextualizedEntityProxy constructor.
	 *
	 * @param \Sm\Data\Entity\EntitySchema               $entitySchema
	 * @param \Sm\Data\Entity\Context\EntityContext|null $context
	 *
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 */
	public function __construct(EntitySchema $entitySchema = null, EntityContext $context = null) {
		parent::__construct($entitySchema, $context);
		if ($entitySchema instanceof EntitySchematic) $context->registerEntitySchematic($entitySchema);
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 */
	public function __get($name) {
		if (!isset($this->subject)) throw new UnresolvableException("Cannot resolve ${name}");
		return $this->subject->$name;
	}

	public function __call($name, $args = []) {
		try {
			$check = new ReflectionMethod($this->subject, $name);

			if ($check->isPrivate() || $check->isProtected()) {
				throw new InvalidArgumentException("Cannot access method");
			}

			return $this->subject->{$name}(...$args);
		} catch (\ReflectionException $e) {
			throw new InvalidArgumentException("Cannot access method");
		}
	}

	public function getName() {
		if (!$this->subject) return null;
		return $this->subject->getName();
	}

	public function setName(string $name) {
		return $this;
	}

	public function getPersistedIdentity(): ?ModelSchema {
		return null;
	}

	public function getProperties() {
		$context = $this->context;
		if (!$this->subject) return new PropertyContainer;

		# get an array of properties
		$contextualizedProperties = $this->getContextualizedPropertyArray($context);
		$propertySchemaContainer  = new PropertyContainer;
		$propertySchemaContainer->register($contextualizedProperties);
		return $propertySchemaContainer;
	}

	public function getSmID(): ?string {
		if (!$this->subject) return null;
		return $this->subject->getSmID();
	}

	public function proxyInContext(Context $context = null): EntitySchema {
		return $this->subject ? $this->subject->proxyInContext($context) : $this;
	}

	public function jsonSerialize() {
		$serialized_properties = [];
		$properties            = $this->getProperties();

		/**
		 * @var \Sm\Data\Property\PropertySchema $property
		 */
		foreach ($properties as $name => $property) {
			if ($property instanceof PropertySchematic)
				$serialized_properties[$name] = $property;
			else if ($property instanceof Property) {
				$value = $property->resolve();
				if ($value instanceof Entity) $value = $value->proxyInContext($this->getContext());
				$serialized_properties[$name] = $value;
			}
		}

		return [
			'smID'       => $this->subject ? $this->subject->getSmID() : null,
			'properties' => $serialized_properties,
		];
	}
	/**
	 * @param $context
	 * @return array
	 */
	private function getContextualizedPropertyArray($context): array {
		$properties               = $this->subject->getProperties();
		$contextualizedProperties = [];
		/**
		 * @var \Sm\Data\Property\Property $property
		 */
		foreach ($properties as $propertyName => $property) {
			$effectiveSchematic = $property instanceof Schematic ? $property : $property->getEffectiveSchematic();
			if (!($effectiveSchematic instanceof EntityPropertySchematic)) {
				continue;
			}
			$contextNames = $effectiveSchematic->getContextNames();
			$contextName  = isset($context) ? $context->getContextName() : null;

			if (!$contextNames || in_array($contextName, $contextNames)) {
				$contextualizedProperties[$propertyName] = $property;
			}
		}
		return $contextualizedProperties;
	}
}