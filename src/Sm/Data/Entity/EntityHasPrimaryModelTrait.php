<?php


namespace Sm\Data\Entity;

use Sm\Core\Event\GenericEvent;
use Sm\Core\Internal\Monitor\Monitor;
use Sm\Data\Entity\Exception\EntityModelNotFoundException;
use Sm\Data\Model\Exception\ModelNotFoundException;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelDataManager;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\PropertyContainer;

/**
 * Trait EntityHasPrimaryModelTrait
 */
trait EntityHasPrimaryModelTrait {
    /** @var  Model $foundModel */
    private $foundModel;
    
    /**
     * @param array $attributes
     * @param int   $context
     *
     * @return \Sm\Data\Entity\Entity
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Entity\Exception\EntityModelNotFoundException
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     */
    public function find($attributes = [], $context = 0) {
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
    /**
     * Search for the Model that this Entity is based
     *
     * @param \Sm\Data\Model\ModelDataManager $modelDataManager
     * @param array                           $attributes
     *
     * @return Model
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Entity\Exception\EntityModelNotFoundException
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     */
    protected function findPersistedIdentity(ModelDataManager $modelDataManager, $attributes = []) {
        $model = $this->getPersistedIdentitySchema($modelDataManager);
        try {
            $model->set($attributes);
            return $this->_searchForPersistedIdentity($modelDataManager, $model);
        } catch (ModelNotFoundException $modelNotFoundException) {
            throw new EntityModelNotFoundException("Could not find the primaryModel associated with this Entity", null, $modelNotFoundException);
        }
    }
    protected function _searchForPersistedIdentity(ModelDataManager $modelDataManager, ModelSchema $model) {
        return $modelDataManager->persistenceManager->find($model);
    }
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
}