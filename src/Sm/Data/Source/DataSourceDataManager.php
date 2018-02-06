<?php


namespace Sm\Data\Source;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Data\SmEntity\SmEntityDataManager;
use Sm\Data\Source\Database\DatabaseSourceSchematic;
use Sm\Data\Source\Database\Table\TableSourceSchematic;
use Sm\Data\Source\Schema\DataSourceSchemaFactory;

/**
 * Class DataSourceManager
 *
 * CLass for resolving
 */
class DataSourceDataManager extends SmEntityDataManager {
    public function configure($configuration): DataSourceSchematic {
        if (!is_array($configuration)) throw new UnimplementedError("Cannot configure anything but arrays");
        
        $type = $configuration['type'] ?? null;
        if (!isset($type)) throw new UnimplementedError("Cannot configure DataSource without a type");
        
        # todo  -- deadline approaching so this isn't implemented in the way that I would prefer. Use a factory.
        switch ($type) {
            case 'table':
                return TableSourceSchematic::init()->load($configuration);
            case 'database':
                return DatabaseSourceSchematic::init()->load($configuration);
            default:
                throw new UnimplementedError("Cannot initialize anything except for a TableSource or DatabaseSource");
        }
    }
    /**
     * Initialize the default SmEntityFactory for this class
     *
     * @return mixed
     */
    protected function initializeDefaultSmEntityFactory(): SmEntityFactory {
        return DataSourceSchemaFactory::init();
    }
}