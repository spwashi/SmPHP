<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Property;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Data\DataLayer;
use Sm\Data\SmEntity\SmEntityDataManager;
use Sm\Data\Type\DatatypeFactory;

/**
 * Class PropertyDataManager
 *
 * Handles the loading/configuration of Properties
 *
 * @method configure($configuration):PropertySchematic
 */
class PropertyDataManager extends SmEntityDataManager implements PropertySchematicInstantiator {
    protected static $identityManagerName = 'Property';
    protected $datatypeFactory;
    
    public function __construct(DataLayer $dataLayer = null,
                                SmEntityFactory $smEntityFactory = null,
                                DatatypeFactory $datatypeFactory = null) {
        $this->setDatatypeFactory($datatypeFactory ?? new DatatypeFactory);
        parent::__construct($dataLayer, $smEntityFactory);
    }
    public function createSchematic(): SmEntitySchematic {
        return PropertySchematic::init();
    }
    public function setDatatypeFactory(DatatypeFactory $datatypeFactory) {
        $this->datatypeFactory = $datatypeFactory;
        return $this;
    }
    protected function createSmEntityFactory(): SmEntityFactory {
        return PropertyFactory::init()->setDatatypeFactory($this->datatypeFactory);
    }
    public function instantiate($schematic = null): Property {
        /** @var \Sm\Data\Property\Property $property */
        $property = parent::instantiate($schematic);
        $property->setDatatypeFactory($this->datatypeFactory);
        return $property;
    }
}