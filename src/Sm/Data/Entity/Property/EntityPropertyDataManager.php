<?php


namespace Sm\Data\Entity\Property;


use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\SmEntity\SmEntityDataManager;

class EntityPropertyDataManager extends PropertyDataManager {
    public function __construct(\Sm\Data\DataLayer $dataLayer = null, SmEntityFactory $smEntityFactory = null, \Sm\Data\Type\DatatypeFactory $datatypeFactory = null) {
        parent::__construct($dataLayer, $smEntityFactory, $datatypeFactory);
    }
    
    public function defaultResolver(string $smID, $schematic = null) {
        if (!($schematic instanceof EntityPropertySchematic)) return null;
        
        $primaryDataType = $schematic->getRawDataTypes();
        $parsed          = SmEntityDataManager::parseSmID($primaryDataType[0] ?? null);
        
        if (!$parsed) return null;
        if (($parsed['manager'] ?? null) !== 'Entity') return null;
        
        $entityAsProperty = new EntityAsProperty;
        /** @var \Sm\Data\Entity\Entity $entity */
        $entity = $this->instantiate($primaryDataType[0]);
        $entityAsProperty->setEntity($entity);
        
        return $entityAsProperty;
    }
    public function createSchematic(): SmEntitySchematic {
        return EntityPropertySchematic::init();
    }
    public function createSmEntityFactory(): SmEntityFactory {
        return EntityPropertyFactory::init()
                                    ->register(FunctionResolvable::init([ $this, 'defaultResolver' ]))
                                    ->setDatatypeFactory($this->datatypeFactory);
    }
}