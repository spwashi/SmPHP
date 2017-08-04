<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Property;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Data\SmEntity\SmEntityDataManager;

/**
 * Class PropertyDataManager
 *
 * Handles the loading/configuration of
 */
class PropertyDataManager extends SmEntityDataManager {
    public function configure($configuration): PropertySchematic {
        return PropertySchematic::init()->load($configuration);
    }
    public function initializeDefaultSmEntityFactory(): SmEntityFactory {
        return PropertyFactory::init();
    }
}