<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Property;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Data\SmEntity\SmEntityDataManager;

/**
 * Class PropertyDataManager
 *
 * Handles the loading/configuration of Properties
 *
 * @method configure($configuration):PropertySchematic
 */
class PropertyDataManager extends SmEntityDataManager implements PropertySchematicInstantiator {
    public function createSchematic(): SmEntitySchematic {
        return PropertySchematic::init();
    }
    protected function createSmEntityFactory(): SmEntityFactory {
        return PropertyFactory::init();
    }
    public function instantiate($schematic = null): Property {
        return parent::instantiate($schematic);
    }
}