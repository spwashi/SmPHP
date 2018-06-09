<?php


namespace Sm\Data\Entity\Property;


use Sm\Core\Context\Context;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\Resolvable;
use Sm\Data\Entity\Entity;
use Sm\Data\Evaluation\Validation\ValidationResult;
use Sm\Data\SmEntity\SmEntityDataManager;
use Sm\Data\Type\Undefined_;

class EntityAsProperty extends EntityProperty {
	/**
	 * @var bool Have we "found" the entity?
	 */
	protected $found = false;
	protected $subject;
	protected $identity;
	/** @var Entity $entity */
	protected $entity;
	protected $attempted_validation_contexts = [];

	#
	##  Resolution
	public function resolve() {
		return $this->entity instanceof Resolvable ? $this->entity->resolve() : $this->entity;
	}


	#
	##  Persistence
	public function find() {
		if ($this->found) {
			return $this->entity;
		} else {
			$this->found = true;
			return $this->entity->find($this->identity);
		}
	}

	public function create(Context $context) {
		if ($this->entity && $this->entity->getPersistedIdentity()) {
			$this->entity->set($this->identity ?? []);
			$this->entity->updateComponentProperties();
			return $this->entity->create($context);
		}
	}


	#
	##  Validation
	public function validate(Context $context = null): ?ValidationResult {
		if ($this->entity) {
			$context_id = $context->getObjectId();

			if (isset($this->attempted_validation_contexts[$context_id])) {
				return $this->attempted_validation_contexts[$context_id];
			}

			return $this->attempted_validation_contexts[$context_id] = $this->entity->validate($context);
		}

		return null;
	}


	#
	##  Getters and Setters
	public function setSubject($subject) {
		if ($subject instanceof Undefined_) {
			return parent::setSubject($subject);
		}

		$effectiveSchematic = $this->entity->getEffectiveSchematic();

		if (!$effectiveSchematic) {
			throw new UnimplementedError("Cannot set subject of entities without Schematics");
		}


		if (is_array($subject) && isset($subject['smID'])) {
			if (SmEntityDataManager::parseSmID($subject['smID']) == SmEntityDataManager::parseSmID($this->entity->getSmID())) {
				$this->entity->set($subject['properties'] ?? []);
				return;
			} else {
				throw new InvalidArgumentException('Wrong Entity Type! Whatchu doin here');
			}
		}

		/** @var \Sm\Data\Property\PropertySchemaContainer $propertySchematics */
		$propertySchematics = $effectiveSchematic->properties;
		foreach ($propertySchematics as $propertySchematic) {
			if (!($propertySchematic instanceof EntityPropertySchematic)) {
				continue;
			}

			if ($propertySchematic->getRole() === EntityPropertySchematic::ROLE__VALUE) {

				$propertySmID = $propertySchematic->getSmID();

				/** @var \Sm\Data\Property\Property $property */
				$property = $this->entity->properties->{$propertySmID};

				if ($property) {
					$property->value = $subject;
					return $this;
				}
			}
		}

		throw new UnimplementedError("Can only set subjects of entities that have a property with a 'value' role");
	}

	public function setEntity(Entity $entity) {
		$this->entity = $entity;
		return $this;
	}

	public function setIdentity($identity) {
		$this->identity = $identity;
		return $this;
	}

	public function getValue(): Entity {
		return $this->entity;
	}


	#
	##  Serialization
	public function jsonSerialize() {
		return $this->entity;
	}
}