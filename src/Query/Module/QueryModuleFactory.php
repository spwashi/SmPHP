<?php
/**
 * User: Sam Washington
 * Date: 7/24/17
 * Time: 7:50 PM
 */

namespace Sm\Query\Module;


use Sm\Core\Factory\Exception\FactoryCannotBuildException;
use Sm\Core\Factory\StandardFactory;
use Sm\Core\Util;

class QueryModuleFactory extends StandardFactory {
    public function build() {
        $arguments = func_get_args();
        try {
            return parent::build(...$arguments);
        } catch (FactoryCannotBuildException $exception) {
            throw new FactoryCannotBuildException("Cannot build module for " . Util::getShapeOfItem(...$arguments), 0, $exception);
        }
    }
    protected function canCreateClass($object_type) {
        return is_a($object_type, QueryModule::class, true);
    }
}