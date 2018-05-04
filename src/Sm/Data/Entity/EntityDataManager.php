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
use Sm\Data\Model\ModelDataManager;
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
    /** @var \Sm\Data\Model\ModelDataManager $modelDataManager */
    protected        $modelDataManager;
    public function __construct(DataLayer $dataLayer = null,
                                SmEntityFactory $smEntityFactory = null,
                                ModelDataManager $datatypeFactory = null) {
        parent::__construct($dataLayer, $smEntityFactory);
        $this->modelDataManager = $datatypeFactory ?? ModelDataManager::init();
    }
    public function createSchematic(): SmEntitySchematic {
        return EntitySchematic::init();
    }
    protected function createSmEntityFactory(): SmEntityFactory {
        return EntityFactory::init();
    }
}