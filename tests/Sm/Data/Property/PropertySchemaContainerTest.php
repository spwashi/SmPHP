<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:52 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Property\Exception\ReadonlyPropertyException;

class PropertySchemaContainerTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Data\Property\PropertySchemaContainer */
    protected $propertySchemaContainer;
    
    public function setUp() {
        $this->propertySchemaContainer = new PropertySchemaContainer;
    }
    
    public function testCanRegisterProperty() {
        $Property = new Property;
        $this->propertySchemaContainer->register('title', $Property);
        $titlePropertySchema = $this->propertySchemaContainer->resolve('title');
        $this->assertInstanceOf(PropertySchema::class, $titlePropertySchema);
        $this->expectException(InvalidArgumentException::class);
        $this->propertySchemaContainer->register('first_name', new \stdClass);
    }
    
    public function testCanRemoveProperty() {
        $this->testCanRegisterProperty();
        $Property = $this->propertySchemaContainer->remove('title');
        $this->assertInstanceOf(Property::class, $Property);
    }
    
    public function testCanMarkReadonlyAndNotRegister() {
        $this->propertySchemaContainer->markReadonly();
        $this->expectException(ReadonlyPropertyException::class);
        $this->testCanRegisterProperty();
    }
    
    public function testCanMarkReadonlyAndNotRemove() {
        $this->testCanRegisterProperty();
        $this->propertySchemaContainer->markReadonly();
        $this->expectException(ReadonlyPropertyException::class);
        $this->propertySchemaContainer->remove('title');
    }
}
