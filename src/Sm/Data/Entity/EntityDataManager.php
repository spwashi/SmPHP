<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Entity;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Data\DataLayer;
use Sm\Data\Entity\Property\EntityPropertyDataManager;
use Sm\Data\Model\ModelDataManager;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\SmEntity\SmEntityDataManager;

/**
 * Class EntityDataManager
 *
 * Handles the loading/configuration of Properties
 *
 * @method configure($configuration):EntitySchematic
 */
class EntityDataManager extends SmEntityDataManager {
    protected static $identityManagerName = 'Entity';
    /** @var ModelDataManager $modelDataManager */
    protected $modelDataManager;
    /** @var PropertyDataManager $propertyDataManager */
    protected $propertyDataManager;
    public function __construct(DataLayer $dataLayer = null,
                                SmEntityFactory $smEntityFactory = null,
                                ModelDataManager $modelDataManager = null,
                                EntityPropertyDataManager $propertyDataManager = null) {
        parent::__construct($dataLayer, $smEntityFactory);
        $this->modelDataManager    = $modelDataManager ?? ModelDataManager::init();
        $this->propertyDataManager = $propertyDataManager ?? EntityPropertyDataManager::init();
    }
    public function createSchematic(): SmEntitySchematic {
        return EntitySchematic::init($this->modelDataManager, $this->propertyDataManager);
    }
    protected function createSmEntityFactory(): SmEntityFactory {
        return EntityFactory::init();
    }
    public function getModelDataManager(): ModelDataManager {
        return $this->modelDataManager;
    }
    /**
     * @return PropertyDataManager
     */
    public function getPropertyDataManager(): PropertyDataManager {
        return $this->propertyDataManager;
    }
}