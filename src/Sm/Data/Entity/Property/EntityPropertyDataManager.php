<?php


namespace Sm\Data\Entity\Property;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Data\Property\PropertyDataManager;

class EntityPropertyDataManager extends PropertyDataManager {
    public function createSchematic(): SmEntitySchematic {
        return EntityPropertySchematic::init();
    }
    public function createSmEntityFactory(): SmEntityFactory {
        return EntityPropertyFactory::init();
    }
}