<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:37 PM
 */

namespace Sm\Data\Property;


use Sm\Data\Type\Integer_;

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
    public function testCanSetDatatypes() {
        $pdm            = PropertyDataManager::init();
        $configuration  = [ 'name' => 'id', 'datatypes' => 'int' ];
        $propertySchema = $pdm->configure($configuration);
        $this->assertInstanceOf(PropertySchematic::class, $propertySchema);
        $datatypes = $propertySchema->getDatatypes();
        $this->assertInstanceOf(Integer_::class, $datatypes[0] ?? null);
    }
}
