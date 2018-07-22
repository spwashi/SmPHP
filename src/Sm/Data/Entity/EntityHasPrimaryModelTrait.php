<?php


namespace Sm\Data\Entity;

use Sm\Core\Context\Context;
use Sm\Core\Event\GenericEvent;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\Monitor;
use Sm\Data\Entity\Context\EntityContext;
use Sm\Data\Entity\Exception\EntityNotFoundException;
use Sm\Data\Entity\Exception\Persistence\CannotModifyEntityException;
use Sm\Data\Entity\Property\EntityProperty;
use Sm\Data\Entity\Property\EntityPropertySchematic;
use Sm\Data\Entity\Validation\EntityValidationResult;
use Sm\Data\Model\Context\ModelCreationContext;
use Sm\Data\Model\Context\ModelSearchContext;
use Sm\Data\Model\Exception\ModelNotFoundException;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelDataManager;
use Sm\Data\Model\ModelInstance;
use Sm\Data\Model\ModelPersistenceManager;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertySchemaContainer;
use Sm\Data\Type\Undefined_;

trait EntityHasPrimaryModelTrait {
	use EntitytraitTrait;
	/** @var  Model $foundModel */
	private $foundModel;


	public function fromModel(Model $model): Entity {
		/** @var Entity $entity */
		$entity = $this->inheritingEntity();

		/** @var EntityProperty[] $allProperties */
		$allProperties = $entity->getProperties()->getAll();

		foreach ($model->getProperties() as $name => $property) {

			/** @var  Property $property */
			if (isset($allProperties[$name])) $entity->set($name, $property->raw_value);
		}

		$entity->setPersistedIdentity($model);

		foreach ($allProperties as $property) $entity->fillPropertyValue($property);

		return $entity;
	}
	public function findPrimaryModel($attributes = [], Context $context = null) {
		$entity = $this->inheritingEntity();

		##  Instantiate Model
		$model_context = new ModelSearchContext;
		if (isset($context)) $model_context->setSituationalContext($context);

		$model = $entity->components->getRepresentativeModel($model_context);

		##  Set Model Properties
		foreach ($model->properties->getAll() as $name => $model_property) {
			if (!isset($attributes[$name])) continue;
			$model->set($name, $attributes[$name]);
		}

		##  Search for the Primary Model
		try {
			$modelDataManager = $entity->entityDataManager->modelDataManager;
			$primaryModel     = $this->searchForModel($modelDataManager, $model);
		} catch (ModelNotFoundException $modelNotFoundException) {
			throw new EntityNotFoundException("Could not find the primaryModel associated with this Entity", null, $modelNotFoundException);
		}

		## Add the even tto a Monitor
		$entityMonitor = $entity->getMonitor(Monitor::INFO);
		$entityMonitor->append(GenericEvent::init('FOUND PRIMARY MODEL -- ', [$primaryModel, $primaryModel->jsonSerialize(),]));

		## Set properties from this Model
		$this->fromModel($primaryModel);

		# Since we've found the model, mark its properties as "unchanged" TODO this shouldn't be here
		$primaryModel->markUnchanged();


		return $entity;
	}

	#
	## Creation
	public function createPrimaryModel(Context $context, $attributes = []): EntityValidationResult {
		/** @var Entity|EntityHasPrimaryModelTrait $entity */
		$entity = $this->inheritingEntity();

		# Set properties on the model
		$primed_detail_arr      = $entity->primeModelModification($context, $attributes);
		$entityValidationResult = $primed_detail_arr[0];

		/** @var Model $model */
		$model_context = (new ModelCreationContext)->setSituationalContext($context);
		$model         = $entity->components->getRepresentativeModel($model_context);

		# Create the model
		$entity->entityDataManager->modelDataManager->persistenceManager->create($model);
		# Return the success of the validation
		return $entityValidationResult;
	}
	public function savePrimaryModel(Context $context, $attributes = []): EntityValidationResult {
		/** @var Entity|EntityHasPrimaryModelTrait $entity */
		$entity = $this->inheritingEntity();

		# Set properties on the model
		$primed_detail_arr      = $entity->primeModelModification($context, $attributes);
		$entityValidationResult = $primed_detail_arr[0];
		$model                  = $primed_detail_arr[1];

		# save the model
		$entity->entityDataManager->modelDataManager->persistenceManager->save($model);

		# Return the success of the validation
		return $entityValidationResult;
	}
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
	protected function getPropertiesForModel(Entity $entity, Context $context = null): array {
		return array_merge_recursive($entity->getProperties()->getAll(), $entity->getInternal());
	}
	private function searchForModel(ModelDataManager $modelDataManager, ModelSchema $model): Model {
		return $modelDataManager->persistenceManager->find($model);
	}
	private function setModelPropertiesFromEntity(Entity $entity, ModelInstance $model, Context $context = null): void {
#
		## Set the relevant properties on the model
		$properties      = $this->getPropertiesForModel($entity, $context);
		$modelProperties = $model->getProperties();

		foreach ($properties as $key => $value) {
			if ($value instanceof Property) $value = $value->getSubject();
			/** @var Property $property */
			$property = $modelProperties->resolve($key);
			if (!isset($property)) continue;
			$property->value = $value;
		}

		var_dump($properties);
	}

	#
	## Validation
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
	protected function primeModelModification(Context $context, $attributes): array {
		$entity = $this->inheritingEntity();

		#
		## Get the Model that we were looking for
		$modelProxy = $entity->components->getRepresentativeModel();

		$attributes = static::getAttributesForCreation($entity, $attributes);
		$entity->set($attributes);

		#
		## BAD
		$entityValidationResult = static::validateEntityOnContext($context, $entity);
		$this->setModelPropertiesFromEntity($entity,
		                                    $modelProxy,
		                                    $context);

		echo json_encode($modelProxy->getModel(), JSON_PRETTY_PRINT);
		## establish the persistedIdentity of this Model
		$model = $modelProxy->getModel();
		$entity->setPersistedIdentity($model);


		return [$entityValidationResult, $modelProxy];
	}
}