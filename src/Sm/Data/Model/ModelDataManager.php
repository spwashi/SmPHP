<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Model;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Data\DataLayer;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\SmEntity\SmEntityDataManager;

/**
 * Class ModelDataManager
 *
 * Handles the loading/configuration of
 */
class ModelDataManager extends SmEntityDataManager {
    /** @var \Sm\Data\Property\PropertyDataManager */
    private $propertyDataManager;
    /**
     * ModelDataManager constructor.
     *
     * @param \Sm\Data\DataLayer                         $dataLayer
     * @param SmEntityFactory                            $smEntityFactory
     * @param \Sm\Data\Property\PropertyDataManager|null $propertyDataManager
     */
    public function __construct(DataLayer $dataLayer = null,
                                SmEntityFactory $smEntityFactory = null,
                                PropertyDataManager $propertyDataManager = null) {
        parent::__construct($dataLayer, $smEntityFactory);
        $this->propertyDataManager = $propertyDataManager ?? PropertyDataManager::init();
    }
    
    public function configure($configuration): ModelSchematic {
        return ModelSchematic::init($this->propertyDataManager)
                             ->load($configuration);
    }
    
    public function initializeDefaultSmEntityFactory(): SmEntityFactory {
        return ModelFactory::init();
    }
}