<?php


namespace Sm\Data\Entity;

use Sm\Core\Context\Context;
use Sm\Core\Event\GenericEvent;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Internal\Monitor\Monitor;
use Sm\Data\Entity\Exception\EntityNotFoundException;
use Sm\Data\Entity\Exception\Persistence\CannotModifyEntityException;
use Sm\Data\Entity\Property\EntityProperty;
use Sm\Data\Entity\Property\EntityPropertySchematic;
use Sm\Data\Entity\Validation\EntityValidationResult;
use Sm\Data\Model\Exception\ModelNotFoundException;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelDataManager;
use Sm\Data\Model\ModelPersistenceManager;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertySchemaContainer;
use Sm\Data\Type\Undefined_;

/**
 * Trait EntityHasPrimaryModelTrait
 */
trait EntityHasPrimaryModelTrait {
	/** @var  Model $foundModel */
	private $foundModel;

	/**
	 * @param Model $model
	 * @return Entity
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 */
	public function fromModel(Model $model): Entity {
		/** @var Entity $entity */
		$entity = $this;
		/** @var EntityProperty[] $allProperties */
		$allProperties = $entity->getProperties()->getAll(true);
		/**
		 * @var                            $name
		 * @var \Sm\Data\Property\Property $property
		 */
		foreach ($model->getProperties() as $name => $property) {
			if (isset($allProperties[$name])) {
				$entity->set($name, $property->raw_value);
			}
		}
		$entity->setPersistedIdentity($model);
		foreach ($allProperties as $property) {
			$entity->fillPropertyValue($property);
		}
		return $entity;
	}

	/**
	 * @param array                         $attributes
	 *
	 * @param \Sm\Core\Context\Context|null $context
	 *
	 * @return \Sm\Data\Entity\Entity
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 * @throws \Sm\Data\Entity\Exception\EntityNotFoundException
	 */
	public function findPrimaryModel($attributes = [], Context $context = null) {
		/** @var \Sm\Data\Entity\Entity|\Sm\Data\Entity\EntityHasPrimaryModelTrait $entity */
		$entity           = $this;
		$modelDataManager = $entity->entityDataManager->getModelDataManager();

		$model = $this->getPersistedIdentitySchema($modelDataManager);
		try {
			foreach ($model->properties->getAll() as $name => $model_property) {
				if (!isset($attributes[$name])) continue;

				$model->set($name, $attributes[$name]);
			}

			var_dump(json_decode(json_encode($model)));

			$primaryModel = $this->_searchForPersistedIdentity($modelDataManager, $model);
		} catch (ModelNotFoundException $modelNotFoundException) {
			throw new EntityNotFoundException("Could not find the primaryModel associated with this Entity", null, $modelNotFoundException);
		}


		$entity->getMonitor(Monitor::INFO)->append(GenericEvent::init('FOUND PRIMARY MODEL -- ',
		                                                              [
			                                                              $primaryModel,
			                                                              $primaryModel->jsonSerialize(),
		                                                              ]));
		$this->fromModel($primaryModel);
		$primaryModel->markUnchanged();
		return $entity;
	}

	#
	## Creation
	/**
	 * @param \Sm\Core\Context\Context $context
	 * @param array                    $attributes
	 *
	 * @return \Sm\Data\Entity\Validation\EntityValidationResult
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Data\Entity\Exception\Persistence\CannotModifyEntityException
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 */
	public function createPrimaryModel(Context $context, $attributes = []): EntityValidationResult {
		/** @var Entity|EntityHasPrimaryModelTrait $entity */
		$entity = $this;

		# Set properties on the model
		$primed_detail_arr      = $this->primeModelModification($context, $attributes);
		$entityValidationResult = $primed_detail_arr[0];
		$model                  = $primed_detail_arr[1];

		# Create the model
		$entity->entityDataManager->modelDataManager->persistenceManager->create($model);

		# Return the success of the validation
		return $entityValidationResult;
	}
	/**
	 * @param Context $context
	 * @param array   $attributes
	 * @return EntityValidationResult
	 * @throws CannotModifyEntityException
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 */
	public function savePrimaryModel(Context $context, $attributes = []): EntityValidationResult {
		/** @var Entity|EntityHasPrimaryModelTrait $entity */
		$entity = $this;

		# Set properties on the model
		$primed_detail_arr      = $this->primeModelModification($context, $attributes);
		$entityValidationResult = $primed_detail_arr[0];
		$model                  = $primed_detail_arr[1];

		# save the model
		$entity->entityDataManager->modelDataManager->persistenceManager->save($model);

		# Return the success of the validation
		return $entityValidationResult;
	}
	/**
	 * @param \Sm\Data\Entity\Entity $entity
	 * @param                        $attributes
	 *
	 * @return array
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 */
	private static function getAttributesForCreation(Entity $entity, $attributes): array {
#
		## Check the arguments
		if ($attributes instanceof ModelSchema) {
			$attributes = $attributes->getProperties();
		}

		if ($attributes instanceof PropertySchemaContainer) {
			$attributes = $attributes->getAll();
		}

		if (!is_array($attributes)) {
			throw new InvalidArgumentException("Can only create these entities from ModelSchemas or PropertySchemaContainers");
		}


		#
		## Set the properties of the Model
		$entity_property_array   = [];
		$entityPropertyContainer = $entity->getProperties();

		/** @var \Sm\Data\Entity\Property\EntityProperty $property */
		foreach ($entityPropertyContainer as $name => $property) {
			if ($property->resolve() instanceof Undefined_) continue;

			$entity_property_array[$name] = $property;
		}
		$attributes = array_merge($entity_property_array, $attributes);

		return $attributes;
	}

	#
	## Finding/Hydrating
	/**
	 * @param \Sm\Data\Entity\Entity   $entity
	 *
	 * @param \Sm\Core\Context\Context $context
	 *
	 * @return array
	 */
	protected function getPropertiesForModel(Entity $entity, Context $context = null): array {
		return array_merge_recursive($entity->getProperties()->getAll(), $entity->getInternal());
	}
	private function _searchForPersistedIdentity(ModelDataManager $modelDataManager, ModelSchema $model): Model {
		return $modelDataManager->persistenceManager->find($model);
	}
	private function setModelPropertiesFromEntity(Entity $entity, Model $model, Context $context = null): void {
#
		## Set the relevant properties on the model
		$properties = $this->getPropertiesForModel($entity, $context);

		foreach ($properties as $key => $value) {

			if ($value instanceof Property) $value = $value->getSubject();

			/** @var Property $property */
			$property = $model->properties->{$key};
			if (!isset($property)) continue;

			$property->setDoStrictResolve(true);
			$property->value = $value;
		}
	}

	#
	## PersistedIdentity
	protected function getPersistedIdentityIdentifyingProperties(): PropertyContainer {
		return PropertyContainer::init();
	}
	/**
	 * @param \Sm\Data\Model\ModelDataManager $modelDataManager
	 *
	 * @return Model
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 */
	private function getPersistedIdentitySchema(ModelDataManager $modelDataManager): ModelSchema {
		/** @var \Sm\Data\Entity\Entity|\Sm\Data\Entity\EntityHasPrimaryModelTrait $self */
		$self             = $this;
		$modelSchema      = $self->getPersistedIdentity();
		$model            = $modelDataManager->instantiate($modelSchema);
		$searchProperties = $self->getPersistedIdentityIdentifyingProperties();
		return $model->set($searchProperties);
	}

	#
	## Validation
	/**
	 * @param \Sm\Core\Context\Context $context
	 * @param                          $entity
	 *
	 * @return mixed
	 * @throws \Sm\Data\Entity\Exception\Persistence\CannotModifyEntityException
	 */
	protected static function validateEntityOnContext(Context $context, Entity $entity) {
		$entityValidationResult = $entity->validate($context);

		#
		## If there were errors in the validation, throw an exception
		if (isset($entityValidationResult) && !$entityValidationResult->isSuccess()) {
			$cannotCreateEntityException = new CannotModifyEntityException($entityValidationResult->getMessage());

			if ($entityValidationResult instanceof EntityValidationResult) {
				$propertyValidationResults = $entityValidationResult->getPropertyValidationResults();

				$cannotCreateEntityException->setFailedProperties($propertyValidationResults);
			}

			throw $cannotCreateEntityException;
		}

		return $entityValidationResult;
	}
	/**
	 * @param Context $context
	 * @param         $attributes
	 * @return array
	 * @throws CannotModifyEntityException
	 * @throws InvalidArgumentException
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 */
	protected function primeModelModification(Context $context, $attributes): array {
		/** @var \Sm\Data\Entity\Entity|\Sm\Data\Entity\EntityHasPrimaryModelTrait $entity */
		$entity = $this;

		#
		## Get the Model that we were looking for

		$model = $entity->getPersistedIdentity();
		if (!($model instanceof Model)) {
			/** @var ModelDataManager $modelDataManager */
			$modelDataManager = $entity->entityDataManager->modelDataManager;
			$schematic        = $this->getPersistedIdentitySchema($modelDataManager);
			$model            = $modelDataManager->instantiate($schematic);
		}
		$attributes = static::getAttributesForCreation($entity, $attributes);
		$entity->set($attributes);
		$entityValidationResult = static::validateEntityOnContext($context, $entity);
		$this->setModelPropertiesFromEntity($entity, $model, $context);
		$entity->setPersistedIdentity($model);
		$entity->updateComponentProperties();
		return [$entityValidationResult, $model];
	}
}