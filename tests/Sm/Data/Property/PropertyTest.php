<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:50 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Resolvable\StringResolvable;
use Sm\Data\Property\Exception\ReadonlyPropertyException;

class PropertyTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Data\Property\Property $property */
    protected $property;
    public function setUp() {
        $this->property = new Property;
    }
    public function testCanCreate() {
        $Property = new Property();
        $Property = Property::init();
        $this->assertInstanceOf(Property::class, $Property);
    }
    public function testCanMarkReadonlyAndNotSet() {
        $this->property->markReadonly();
        $this->expectException(ReadonlyPropertyException::class);
        $this->testCanSetValue();
    }
    public function testCanSetValue() {
        $this->property->value = 'sam';
        $this->assertEquals('sam', $this->property->value);
        $this->assertInstanceOf(StringResolvable::class, $this->property->raw_value);
    }
}
