<?php


namespace Sm\Data\Entity;

use Sm\Data\Entity\Exception\EntityModelNotFoundException;
use Sm\Data\Model\Exception\ModelNotFoundException;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelDataManager;
use Sm\Data\Property\PropertyContainer;

/**
 * Trait EntityHasPrimaryModelTrait
 */
trait EntityHasPrimaryModelTrait {
    /** @var  Model $primaryModel */
    private $primaryModel;
    
    /**
     * Get the properties that are to be used to identify the Model we want to find
     *
     * @param null|string $context
     *
     * @return \Sm\Data\Property\PropertyContainer
     */
    abstract protected function getPrimaryModelProperties($context = null): PropertyContainer;
    /**
     * Search for the Model that this Entity is based
     *
     * @param \Sm\Data\Model\ModelDataManager $modelDataManager
     * @param null                            $context
     *
     * @return Model
     * @throws \Sm\Data\Entity\Exception\EntityModelNotFoundException
     */
    protected function findPrimaryModel(ModelDataManager $modelDataManager, $context = null) {
        $model = $this->getPrimaryModel($modelDataManager, $context);
        
        try {
            return $this->_searchForPrimaryModel($modelDataManager, $model);
        } catch (ModelNotFoundException $modelNotFoundException) {
            throw new EntityModelNotFoundException("Could not find the primaryModel associated with this Entity", null, $modelNotFoundException);
        }
    }
    protected function _searchForPrimaryModel(ModelDataManager $modelDataManager, $model) {
        return $modelDataManager->persistenceManager->find($model);
    }
    /**
     * @param \Sm\Data\Model\ModelDataManager $modelDataManager
     * @param                                 $context
     *
     * @return Model
     */
    protected function getPrimaryModel(ModelDataManager $modelDataManager, $context = null): Model {
        $model            = $modelDataManager->instantiate($this->primaryModelName);
        $searchProperties = $this->getPrimaryModelProperties($context);
    
        return $model->set($searchProperties);
    }
}