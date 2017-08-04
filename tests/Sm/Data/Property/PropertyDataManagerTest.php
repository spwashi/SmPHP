<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:37 PM
 */

namespace Sm\Data\Property;


class PropertyDataManagerTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolveDefaultProperty() {
        $pdm = PropertyDataManager::init();
        $this->assertInstanceOf(Property::class, $pdm->load(null));
    }
    public function testCanConfigureProperty() {
        $pdm            = PropertyDataManager::init();
        $configuration  = [ 'name' => 'id' ];
        $propertySchema = $pdm->configure($configuration);
        $this->assertInstanceOf(PropertySchematic::class, $propertySchema);
        $this->assertEquals('id', $propertySchema->getName());
    }
}
