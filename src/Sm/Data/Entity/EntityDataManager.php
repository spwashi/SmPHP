<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Entity;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Data\DataLayer;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\SmEntity\SmEntityDataManager;

/**
 * Class EntityDataManager
 *
 * Handles the loading/configuration of
 */
class EntityDataManager extends SmEntityDataManager {
    protected $configuredEntitys = [];
    /** @var \Sm\Data\Property\PropertyDataManager */
    private $propertyDataManager;
    /**
     * EntityDataManager constructor.
     *
     * @param \Sm\Data\DataLayer                         $dataLayer
     * @param SmEntityFactory                            $smEntityFactory
     * @param \Sm\Data\Property\PropertyDataManager|null $datatypeFactory
     */
    public function __construct(DataLayer $dataLayer = null,
                                SmEntityFactory $smEntityFactory = null,
                                PropertyDataManager $datatypeFactory = null) {
        parent::__construct($dataLayer, $smEntityFactory);
        $this->propertyDataManager = $datatypeFactory ?? PropertyDataManager::init();
    }
    
    public function configure($configuration): EntitySchematic {
        $item = EntitySchematic::init($this->propertyDataManager)
                               ->load($configuration);
        $smID = $item->getSmID();
        
        #
        if ($smID) $this->configuredEntitys[ $smID ] = $item;
        
        #
        return $item;
    }
    
    public function initializeDefaultSmEntityFactory(): SmEntityFactory {
        return EntityFactory::init();
    }
    /**
     * @return \Sm\Data\Entity\EntitySchematic[]
     */
    public function getConfiguredEntitys(): array {
        return $this->configuredEntitys;
    }
}