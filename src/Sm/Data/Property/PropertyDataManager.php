<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:21 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Schema\Schematic;
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
    /** @var \Sm\Data\Property\PropertyFactory */
    private $propertyFactory;
    public function __construct(DataLayer $dataLayer = null,
                                SmEntityFactory $smEntityFactory = null,
                                DatatypeFactory $datatypeFactory = null,
                                PropertyFactory $propertyFactory = null) {
        parent::__construct($dataLayer, $smEntityFactory);
        $this->datatypeFactory = $datatypeFactory ?? DatatypeFactory::init();
        $this->propertyFactory = $propertyFactory ?? PropertyFactory::init();
    }
    public function configure($configuration): PropertySchematic {
        return PropertySchematic::init()->load($configuration);
    }
    public function instantiate($schematic = null) {
        if ($schematic && !($schematic instanceof Schematic)) throw new InvalidArgumentException("Can only use Schematics to initialize PropertyDataManagers");
        return $this->propertyFactory->resolve(null, $schematic)->fromSchematic($schematic);
    }
    public function initializeDefaultSmEntityFactory(): SmEntityFactory {
        return PropertyFactory::init();
    }
}