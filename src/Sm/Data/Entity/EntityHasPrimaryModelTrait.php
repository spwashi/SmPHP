<?php


namespace Sm\Data\Entity;

use Sm\Core\Context\Context;
use Sm\Core\Event\GenericEvent;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Internal\Monitor\Monitor;
use Sm\Data\Entity\Exception\EntityNotFoundException;
use Sm\Data\Entity\Exception\Persistence\CannotCreateEntityException;
use Sm\Data\Entity\Validation\EntityValidationResult;
use Sm\Data\Model\Exception\ModelNotFoundException;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelDataManager;
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
     * @param array $attributes
     *
     * @return \Sm\Data\Entity\Entity
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Entity\Exception\EntityNotFoundException
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
     */
    public function find($attributes = [], Context $context = null) {
        /** @var \Sm\Data\Entity\Entity|\Sm\Data\Entity\EntityHasPrimaryModelTrait $entity */
        $entity           = $this;
        $modelDataManager = $entity->entityDataManager->getModelDataManager();
        $primaryModel     = $entity->findPersistedIdentity($modelDataManager, $attributes);
        $entity->getMonitor(Monitor::INFO)->append(GenericEvent::init('FOUND PRIMARY MODEL -- ',
                                                                      [
                                                                          $primaryModel,
                                                                          $primaryModel->jsonSerialize(),
                                                                      ]));
        $allProperties = $entity->getProperties()->getAll();
        /**
         * @var                            $name
         * @var \Sm\Data\Property\Property $property
         */
        foreach ($primaryModel->getProperties() as $name => $property) {
            if (isset($allProperties[ $name ])) {
                $entity->set($name, $property->raw_value);
            }
        }
        $this->setPersistedIdentity($primaryModel);
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
     * @throws \Sm\Data\Entity\Exception\Persistence\CannotCreateEntityException
     * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
     */
    public function create(Context $context, $attributes = []): EntityValidationResult {
        /** @var \Sm\Data\Entity\Entity|\Sm\Data\Entity\EntityHasPrimaryModelTrait $entity */
        $entity = $this;
        
        #
        ## Get the Model that we were looking for
        $modelDataManager = $entity->entityDataManager->getModelDataManager();
        $schematic        = $this->getPersistedIdentitySchema($modelDataManager);
        $attributes       = static::getAttributesForCreation($entity, $attributes);
        
        $entity->set($attributes);
        $entityValidationResult = static::validateEntityOnContext($context, $entity);
        
        
        $model = $modelDataManager->instantiate($schematic);
        $this->setModelPropertiesFromEntity($entity, $model);
        
        #
        ## Throws an error if there was one
        $modelPersistenceManager = $modelDataManager->persistenceManager;
        $modelPersistenceManager->create($model);
        $entity->setPersistedIdentity($model);
        
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
    protected static function getAttributesForCreation(Entity $entity, $attributes): array {
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
            
            $entity_property_array[ $name ] = $property;
        }
        $attributes = array_merge($entity_property_array, $attributes);
        
        return $attributes;
    }
    
    #
    ## Finding/Hydraying
    /**
     * Search for the Model that this Entity is based
     *
     * @param \Sm\Data\Model\ModelDataManager $modelDataManager
     * @param array                           $attributes
     *
     * @return Model
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Entity\Exception\EntityNotFoundException
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     */
    protected function findPersistedIdentity(ModelDataManager $modelDataManager, $attributes = []) {
        $model = $this->getPersistedIdentitySchema($modelDataManager);
        try {
            $model->set($attributes);
            return $this->_searchForPersistedIdentity($modelDataManager, $model);
        } catch (ModelNotFoundException $modelNotFoundException) {
            throw new EntityNotFoundException("Could not find the primaryModel associated with this Entity", null, $modelNotFoundException);
        }
    }
    protected function _searchForPersistedIdentity(ModelDataManager $modelDataManager, ModelSchema $model) {
        return $modelDataManager->persistenceManager->find($model);
    }
    
    #
    ## PersistedIdentity
    /**
     * @param \Sm\Data\Model\ModelDataManager $modelDataManager
     *
     * @return Model
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    protected function getPersistedIdentitySchema(ModelDataManager $modelDataManager): ModelSchema {
        /** @var \Sm\Data\Entity\Entity|\Sm\Data\Entity\EntityHasPrimaryModelTrait $self */
        $self             = $this;
        $modelSchema      = $self->getPersistedIdentity();
        $model            = $modelDataManager->instantiate($modelSchema);
        $searchProperties = $self->getPersistedIdentityIdentifyingProperties();
        return $model->set($searchProperties);
    }
    protected function getPersistedIdentityIdentifyingProperties(): PropertyContainer {
        return PropertyContainer::init();
    }
    
    #
    ## Validation
    /**
     * @param \Sm\Core\Context\Context $context
     * @param                          $entity
     *
     * @return mixed
     * @throws \Sm\Data\Entity\Exception\Persistence\CannotCreateEntityException
     */
    protected static function validateEntityOnContext(Context $context, Entity $entity) {
        $entityValidationResult = $entity->validate($context);
        
        #
        ## If there were errors in the validation, throw an exception
        if (isset($entityValidationResult) && !$entityValidationResult->isSuccess()) {
            $cannotCreateEntityException = new CannotCreateEntityException($entityValidationResult->getMessage());
            
            if ($entityValidationResult instanceof EntityValidationResult) {
                $propertyValidationResults = $entityValidationResult->getPropertyValidationResults();
                
                $cannotCreateEntityException->setFailedProperties($propertyValidationResults);
            }
            
            throw $cannotCreateEntityException;
        }
        return $entityValidationResult;
    }
    /**
     * @param \Sm\Data\Entity\Entity $entity
     * @param                        $model
     */
    public function setModelPropertiesFromEntity(Entity $entity, Model $model): void {
#
        ## Set the relevant properties on the model
        $properties = $this->getPropertiesForModel($entity);
        foreach ($properties as $key => $value) {
            
            if ($value instanceof Property) $value = $value->getSubject();
            
            /** @var \WANGHORN\Model\Property $property */
            $property = $model->properties->{$key};
            if (!isset($property)) continue;
            
            $property->setDoStrictResolve(true);
            $property->value = $value;
        }
    }
    /**
     * @param \Sm\Data\Entity\Entity $entity
     *
     * @return array
     */
    protected function getPropertiesForModel(Entity $entity): array {
        return array_merge_recursive($entity->getProperties()->getAll(), $entity->getInternal());
}
}