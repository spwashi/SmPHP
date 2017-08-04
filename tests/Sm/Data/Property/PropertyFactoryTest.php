<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:49 PM
 */

namespace Sm\Data\Property;


class PropertyFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolvePropertyByDefaultWithNoParameters() {
        $factory = new PropertyFactory;
        $this->assertInstanceOf(Property::class, $factory->resolve());
    }
}
