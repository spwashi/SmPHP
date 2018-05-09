<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Model;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Data\DataLayer;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\SmEntity\SmEntityDataManager;

/*
 * Class ModelDataManager
 *
 * Handles the loading/configuration of
 */

/**
 * @property  ModelPersistenceManager $persistenceManager
 *
 * @method configure($configuration): ModelSchematic
 */
class ModelDataManager extends SmEntityDataManager {
    protected static $identityManagerName = 'Model';
    /** @var  ModelPersistenceManager $persistenceManager */
    protected $persistenceManager;
    /** @var PropertyDataManager */
    private $propertyDataManager;
    /**
     * ModelDataManager constructor.
     *
     * @param \Sm\Data\DataLayer       $dataLayer
     * @param SmEntityFactory          $smEntityFactory
     * @param PropertyDataManager|null $datatypeFactory
     */
    public function __construct(DataLayer $dataLayer = null,
                                SmEntityFactory $smEntityFactory = null,
                                PropertyDataManager $datatypeFactory = null) {
        $this->propertyDataManager = $datatypeFactory ?? PropertyDataManager::init();
        parent::__construct($dataLayer, $smEntityFactory);
    }
    
    public function __get($name) {
        switch ($name) {
            case 'persistenceManager':
                return $this->persistenceManager;
        }
    }
    
    protected function createSmEntityFactory(): SmEntityFactory {
        return ModelFactory::init()->setPropertyInstantiatior($this->propertyDataManager);
    }
    protected function createSchematic(): SmEntitySchematic {
        return ModelSchematic::init($this->propertyDataManager);
    }
    public function setPersistenceManager(ModelPersistenceManager $persistenceManager) {
        $this->persistenceManager = $persistenceManager;
        return $this;
    }
    public function getPropertyDataManager(): PropertyDataManager {
        return $this->propertyDataManager;
    }
}