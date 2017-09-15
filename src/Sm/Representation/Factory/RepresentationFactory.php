<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 5:41 PM
 */

namespace Sm\Representation\Factory;


use Sm\Core\Factory\StandardFactory;
use Sm\Representation\Context\RepresentationContext;
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
     * @param null                                                  $item
     * @param \Sm\Representation\Context\RepresentationContext|null $context The context in which we are representing this item
     *
     * @return \Sm\Representation\Representation
     * @internal param null $name
     */
    public function resolve($item = null, RepresentationContext $context = null): Representation {
        return parent::resolve($item, $context);
    }
    
    public function shouldBuildClassInstance($class_name): bool {
        return false;
    }
}