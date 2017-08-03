<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:26 PM
 */

namespace Sm\Core\Factory;


class FactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Factory = $this->getMockForAbstractClass(StandardFactory::class);
        $this->assertInstanceOf(StandardFactory::class, $Factory);
    }
    public function testCanCreateClasses() {
        /** @var \Sm\Core\Factory\StandardFactory $Factory */
        $Factory = $this->getMockForAbstractClass(StandardFactory::class);
        $this->assertInstanceOf(\stdClass::class, $Factory->build(\stdClass::class));
    }
    public function testCanRegister() {
        $return_eleven = function () { return 11; };
        /** @var \Sm\Core\Factory\StandardFactory $Factory */
        $Factory = $this->getMockForAbstractClass(StandardFactory::class);
        
        $Factory->register(null, $return_eleven);
        $response = $Factory->build();
        $this->assertEquals(11, $response);
    
        /** @var \Sm\Core\Factory\StandardFactory $Factory */
        $Factory = $this->getMockForAbstractClass(StandardFactory::class);
        
        
        $add_1 = function (int $int) { return $int + 1; };
        $Factory->register(null, $add_1);
        $response_1 = $Factory->build(2);
        $response_2 = $Factory->build(1);
        $this->assertEquals(3, $response_1);
        $this->assertNotEquals(3, $response_2);
        
        $Mock = $this->getMockBuilder(\stdClass::class)
                     ->setMethods([ 'test' ])->getMock();
        $Mock->method('test')->willReturn('test_works');
    
        ###
        /** @var \Sm\Core\Factory\StandardFactory $Factory */
        $Factory = $this->getMockForAbstractClass(StandardFactory::class);
        $Factory->register(\stdClass::class, $Mock);
        $MockFromFactory = $Factory->build(\stdClass::class);
        $this->assertEquals('test_works', $MockFromFactory->test());
        ###
    }
}
