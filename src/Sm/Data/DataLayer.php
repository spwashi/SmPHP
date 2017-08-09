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
use Sm\Core\SmEntity\Exception\InvalidConfigurationException;
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
        $data_manager = $this->getDataManager($name);
        if ($data_manager) return $data_manager;
        throw new InvalidArgumentException("Cannot resolve {$name}");
    }
    
    public function getDataManager($name): SmEntityDataManager {
        switch ($name) {
            case 'models':
            case '[Model]':
                return $this->initStdSmEntityManager(Model::class);
            case 'properties':
            case '[Property]':
                return $this->initStdSmEntityManager(Property::class);
            case 'sources':
            case '[DataSource]':
                return $this->initStdSmEntityManager(DataSource::class);
        }
        throw new InvalidArgumentException("Cannot resolve {$name} manager");
    }
    
    public function configure(array $configuration_array) {
        /** @var \stdClass $configuration */
        foreach ($configuration_array as $configuration) {
            if (!isset($configuration['smID'])) continue;
            
            $smID = $configuration['smID'];
            if (!is_string($smID)) {
                $item_json = json_encode($configuration);
                throw new InvalidConfigurationException("Cannot configure-  malformed smID in {$item_json}");
            }
            
            $regex = '(^\[[a-zA-Z_]+])(.+)'; # [Type]name   e.g.   [Model]users
            preg_match("~{$regex}~", $smID, $matches);
            
            $config_type = $matches[1] ?? null;
            if (!$config_type) continue; # assume this isn't an SmEntity handled by this Data layer?
            
            $dataManager = $this->getDataManager($config_type);
            
            $result = $dataManager->configure($configuration);
            var_dump(json_decode(json_encode($result), 1));
        }
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