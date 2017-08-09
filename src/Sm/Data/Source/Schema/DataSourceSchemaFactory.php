<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 9:49 PM
 */

namespace Sm\Data\Source\Schema;


use Sm\Core\Factory\Exception\WrongFactoryException;
use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Data\Source\DiscretelySourced;

class DataSourceSchemaFactory extends SmEntityFactory {
    public function resolve($name = null): DataSourceSchema {
        try {
            return parent::resolve(...func_get_args());
        } catch (WrongFactoryException $e) {
            # If we are trying to get the source of something that has a source, return that source
            if ($name instanceof DiscretelySourced && ($result = $name->getDataSource())) {
                return $name->getDataSource();
            }
            throw $e;
        }
    }
    protected function canCreateClass($object_type) {
        return is_a($object_type, DataSourceSchema::class);
    }
}