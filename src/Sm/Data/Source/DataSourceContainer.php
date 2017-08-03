<?php
/**
 * User: Sam Washington
 * Date: 7/7/17
 * Time: 8:35 AM
 */

namespace Sm\Data\Source;


use Sm\Core\Container\Mini\MiniContainer;

/**
 * Class DataSourceContainer
 *
 * Class to ke
 *
 * @package Sm\Data\Source
 */
class DataSourceContainer extends MiniContainer {
    const DEFAULT_SOURCE = 'default';
    public function getDefault(): DataSource {
        return $this->resolve(DataSourceContainer::DEFAULT_SOURCE) ?? NullDataSource::init();
    }
    /**
     * @inheritdoc
     *
     * @param null|string $name
     * @param null        $registrand
     *
     * @return $this
     */
    public function register($name = DataSourceContainer::DEFAULT_SOURCE, $registrand = null) {
        parent::register($name, $registrand);
        return $this;
    }
    /**
     * @inheritdoc
     *
     * @param null|string $name
     *
     * @return mixed|null
     */
    public function resolve($name = DataSourceContainer::DEFAULT_SOURCE) {
        
        return parent::resolve($name);
    }
    
}