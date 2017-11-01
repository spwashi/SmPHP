<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Model;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Data\DataLayer;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\SmEntity\SmEntityDataManager;

/*
 * Class ModelDataManager
 *
 * Handles the loading/configuration of
 */

class ModelDataManager extends SmEntityDataManager {
    protected $configuredModels = [];
    /** @var \Sm\Data\Property\PropertyDataManager */
    private $propertyDataManager;
    /**
     * ModelDataManager constructor.
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
    public function instantiate($schematic = null) {
        if (is_string($schematic)) {
            if (isset($this->configuredModels[ $schematic ])) {
                $schematic = $this->configuredModels[ $schematic ];
            } else {
                throw new InvalidArgumentException("Cannot find Model to match '{$schematic}'");
            }
        }
        return parent::instantiate($schematic);
    }
    
    public function configure($configuration): ModelSchematic {
        $item = ModelSchematic::init($this->propertyDataManager)->load($configuration);
        $smID = $item->getSmID();
        
        #
        if ($smID) $this->configuredModels[ $smID ] = $item;
        
        #
        return $item;
    }
    
    public function initializeDefaultSmEntityFactory(): SmEntityFactory {
        return ModelFactory::init();
    }
    /**
     * @return \Sm\Data\Model\ModelSchematic[]
     */
    public function getConfiguredModels(): array {
        return $this->configuredModels;
    }
}