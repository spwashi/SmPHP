<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Property;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Data\DataLayer;
use Sm\Data\SmEntity\SmEntityDataManager;
use Sm\Data\Type\DatatypeFactory;

/**
 * Class PropertyDataManager
 *
 * Handles the loading/configuration of
 */
class PropertyDataManager extends SmEntityDataManager {
    protected $datatypeFactory;
    public function __construct(DataLayer $dataLayer = null,
                                SmEntityFactory $smEntityFactory = null,
                                DatatypeFactory $datatypeFactory = null) {
        parent::__construct($dataLayer, $smEntityFactory);
        $this->datatypeFactory = $datatypeFactory ?? DatatypeFactory::init();
    }
    public function configure($configuration): PropertySchematic {
        return PropertySchematic::init()->load($configuration);
    }
    public function initializeDefaultSmEntityFactory(): SmEntityFactory {
        return PropertyFactory::init();
    }
}