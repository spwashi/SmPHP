<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:27 AM
 */

namespace Sm\Data;


use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Model\Model;
use Sm\Data\Model\ModelDataManager;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\SmEntity\SmEntityDataManager;
use Sm\Data\Source\DataSource;
use Sm\Data\Source\DataSourceDataManager;

/**
 * Class DataLayer
 *
 * @property-read PropertyDataManager   $properties
 * @property-read DataSourceDataManager $sources
 * @property-read ModelDataManager      $models
 * @package Sm\Data
 */
class DataLayer extends StandardLayer {
    /** @var  SmEntityDataManager[] */
    protected $managers;
    
    public function __get($name) {
        switch ($name) {
            case 'models':
                return $this->initStdSmEntityManager(Model::class);
            case 'properties':
                return $this->initStdSmEntityManager(Property::class);
            case 'sources':
                return $this->initStdSmEntityManager(DataSource::class);
        }
        throw new InvalidArgumentException("Cannot resolve {$name}");
    }
    
    /**
     * Initialize an SmEntityFactory to go along with the classname that we provide.
     *
     * @param $classname
     *
     * @return \Sm\Core\SmEntity\SmEntityFactory
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function initStdSmEntityManager(string $classname): SmEntityDataManager {
        if (!class_exists($classname)) throw new UnimplementedError("Cannot resolve objects of type {$classname}");
        
        if (is_a(DataSource::class, $classname, true)) {
            return $this->managers[ DataSource::class ] = $this->managers[ DataSource::class ] ?? DataSourceDataManager::init($this);
        }
        if (is_a(Model::class, $classname, true)) {
            return $this->managers[ Model::class ] = $this->managers[ Model::class ] ?? ModelDataManager::init($this);
        }
        if (is_a(Property::class, $classname, true)) {
            return $this->managers[ Property::class ] = $this->managers[ Property::class ] ?? PropertyDataManager::init($this);
        }
        
        throw new UnimplementedError("Cannot resolve objects of type {$classname}");
    }
    
}