<?php
/**
 * User: spwashi2
 * Date: 1/26/2017
 * Time: 2:47 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Core\Factory\FactoryContainer;

class ResolvableTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Resolvable = $this->getMockForAbstractClass(AbstractResolvable::class, [ null ]);
        $this->assertInstanceOf(AbstractResolvable::class, $Resolvable);
        return $Resolvable;
    }
    public function testCanInvoke() {
        /** @var AbstractResolvable|\PHPUnit_Framework_MockObject_MockObject $Resolvable */
        $Resolvable = $this->getMockForAbstractClass(AbstractResolvable::class, [ null ]);
        
        $Resolvable->expects($this->any())
                   ->method('resolve')
                   ->will($this->returnValue('87'));
        
        $this->assertEquals('87', $Resolvable());
    }
    public function testCanMakeResolvableFactory() {
        /** @var AbstractResolvable $Resolvable */
        $Resolvable = $this->getMockForAbstractClass(AbstractResolvable::class, [ null ]);
        $this->assertInstanceOf(FactoryContainer::class, $Resolvable->getFactoryContainer());
    
        $Resolvable = $this->getMockForAbstractClass(AbstractResolvable::class, [ null ]);
        $Resolvable->setFactoryContainer(new FactoryContainer);
        $this->assertInstanceOf(FactoryContainer::class, $Resolvable->getFactoryContainer());
    }
}
