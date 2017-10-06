<?php


namespace Sm\Data\Entity;


use Sm\Data\Source\Schema\DataSourceSchema;

trait EntityTrait {
    protected $_dataSource;
    
    public function getDataSource(): DataSourceSchema {
        return $this->_dataSource;
    }
    public function setDataSource(DataSourceSchema $dataSource) {
        $this->_dataSource = $dataSource;
        return $this;
    }
}