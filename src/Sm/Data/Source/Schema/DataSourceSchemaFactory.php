<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 9:49 PM
 */

namespace Sm\Data\Source\Schema;


use Sm\Core\Factory\Exception\WrongFactoryException;
use Sm\Core\Factory\StandardFactory;
use Sm\Data\Source\DiscretelySourced;

class DataSourceSchemaFactory extends StandardFactory {
    public function resolve($name = null): DataSourceSchema {
        try {
            return parent::resolve($name);
        } catch (WrongFactoryException $e) {
            if ($name instanceof DiscretelySourced && ($result = $name->getDataSourceSchema())) {
                return $name->getDataSourceSchema();
            }
            throw $e;
        }
    }
    protected function canCreateClass($object_type) {
        return is_a($object_type, DataSourceSchema::class);
    }
}