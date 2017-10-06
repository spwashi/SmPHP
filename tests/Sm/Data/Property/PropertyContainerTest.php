<?php

namespace Sm\Data\Property;

/**
 * Class PropertyContainerTest
 */
class PropertyContainerTest extends \PHPUnit_Framework_TestCase {
    public function testCanSetPropertyValue() {
        $propertyContainer = PropertyContainer::init();
        
        $propertyContainer->register('title', Property::init());
        
        $propertyContainer->title = 'hello';
        
        $this->assertEquals('hello', "{$propertyContainer->title}");
    }
}
