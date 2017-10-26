<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 5:41 PM
 */

namespace Sm\Representation\Factory;


use Sm\Core\Factory\StandardFactory;
use Sm\Representation\Representation;

/**
 * Class RepresentationFactory
 *
 * Factory to build Representations
 *
 */
class RepresentationFactory extends StandardFactory {
    protected function canCreateClass($object_type) {
        return is_a($object_type, Representation::class);
    }
    
    /**
     *
     *
     * @param null $item
     *
     * @return \Sm\Representation\Representation
     * @internal param null $name
     */
    public function resolve($item = null): Representation {
        return parent::resolve(...func_get_args());
    }
    
    public function shouldBuildClassInstance($class_name): bool {
        return false;
    }
}