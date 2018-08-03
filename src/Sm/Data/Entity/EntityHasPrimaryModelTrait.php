<?php


namespace Sm\Data\Entity;

use Sm\Core\Context\Context;
use Sm\Core\Event\GenericEvent;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\InvalidReturnException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\Monitor;
use Sm\Data\Entity\Context\EntityContext;
use Sm\Data\Entity\Exception\EntityNotFoundException;
use Sm\Data\Entity\Exception\Persistence\CannotModifyEntityException;
use Sm\Data\Entity\Property\EntityProperty;
use Sm\Data\Entity\Property\EntityPropertySchematic;
use Sm\Data\Entity\Validation\EntityValidationResult;
use Sm\Data\Evaluation\Validation\ValidationResult;
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


    public function fromModel(ModelInstance $model): Entity {
        /** @var Entity $entity */
        $entity = $this->inheritingEntity();


        /** @var EntityProperty[] $allProperties */
        $allProperties = $entity->getProperties()->getAll();
        /** @var Property[] $modelProperties */
        $modelProperties = $model->getProperties();


        foreach ($modelProperties as $name => $property) {
            if (!isset($allProperties[$name])) continue;

            $entity->set($name, $property->raw_value);
        }

        $entity->setPersistedIdentity($model);
        $entity->components->update();

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

        return $entity;
    }

    #
    ## Creation
    public function createPrimaryModel(Context $context, $attributes = []): EntityValidationResult {
        /** @var Entity|EntityHasPrimaryModelTrait $entity */
        $entity = $this->inheritingEntity();

        # Set properties on the model
        $entityValidationResult = static::validateEntityOnContext($context, $entity);

        /** @var Model $model */
        $model_context = (new ModelCreationContext)->setSituationalContext($context);
        $model         = $entity->components->getRepresentativeModel($model_context);

        # Create the model
        $entity->entityDataManager->modelDataManager->persistenceManager->create($model);

        $this->fromModel($model);

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

    #
    ## Finding/Hydrating
    protected function getPropertiesForModel(Entity $entity, Context $context = null): array {
        return array_merge_recursive($entity->getProperties()->getAll());
    }
    private function searchForModel(ModelDataManager $modelDataManager, ModelSchema $model): Model {
        return $modelDataManager->persistenceManager->find($model);
    }

    #
    ## Validation
    protected static function validateEntityOnContext(Context $context, Entity $entity): EntityValidationResult {
        $entityValidationResult = $entity->validate($context);

        if (!isset($entityValidationResult)) return null;
        if (!$entityValidationResult instanceof EntityValidationResult) throw new InvalidReturnException("Expected an EntityValidationResult");

        #
        ## If there were errors in the validation, throw an exception
        if (!$entityValidationResult->isSuccess()) {
            $cannotCreateEntityException = new CannotModifyEntityException($entityValidationResult->getMessage());
            $propertyValidationResults   = $entityValidationResult->getPropertyValidationResults();
            $cannotCreateEntityException->setFailedProperties($propertyValidationResults);

            throw $cannotCreateEntityException;
        }

        return $entityValidationResult;
    }
}