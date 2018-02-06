<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Model;


use Sm\Core\Exception\InvalidArgumentException;
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
 */
class ModelDataManager extends SmEntityDataManager {
    protected $configuredModels = [];
    /** @var  ModelPersistenceManager $persistenceManager */
    protected $persistenceManager;
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
    public function __get($name) {
        switch ($name) {
            case 'persistenceManager':
                return $this->persistenceManager;
        }
    }
    
    /**
     * Register a classname to instantiate based on the SmID provided
     *
     * @param $smID
     * @param $classname
     */
    public function registerResolver(callable $resolver) {
        $this->getSmEntityFactory()
             ->register(null,
                 function ($type = null, $schematic = null) use ($resolver) {
                     if (!($schematic instanceof SmEntitySchematic)) {
                         return null;
                     }
                
                     return $resolver($schematic->getSmID(), $schematic);
                 });
    }
    
    public function instantiate($schematic = null): Model {
        if (is_string($schematic)) {
            if (!isset($this->configuredModels[ $schematic ])) {
                if (strpos($schematic, '[Model]') !== 0) {
                    return $this->instantiate('[Model]' . $schematic);
                }
                throw new InvalidArgumentException("Cannot find Model to match '{$schematic}'");
            }
    
            $schematic = $this->configuredModels[ $schematic ];
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
    
    protected function initializeDefaultSmEntityFactory(): SmEntityFactory {
        return ModelFactory::init();
    }
    /**
     * @return \Sm\Data\Model\ModelSchematic[]
     */
    public function getConfiguredModels(): array {
        return $this->configuredModels;
    }
    public function setPersistenceManager(ModelPersistenceManager $persistenceManager) {
        $this->persistenceManager = $persistenceManager;
        return $this;
    }
}