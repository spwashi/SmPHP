<?php


namespace Sm\Data\Model;


use Sm\Data\Source\Schema\DataSourceSchema;

trait ModelTrait {
    protected $_dataSource;
    
    public function getDataSource(): DataSourceSchema {
        return $this->_dataSource;
    }
    public function setDataSource(DataSourceSchema $dataSource) {
        $this->_dataSource = $dataSource;
        return $this;
    }
}