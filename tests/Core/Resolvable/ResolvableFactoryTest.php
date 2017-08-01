<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 8:36 PM
 */

namespace Sm\Core\Resolvable;


class ResolvableFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreateResolvableFactory() {
        $ResolvableFactory = new ResolvableFactory;
        $this->assertInstanceOf(ResolvableFactory::class, $ResolvableFactory);
        $ResolvableFactory = ResolvableFactory::init();
        $this->assertInstanceOf(ResolvableFactory::class, $ResolvableFactory);
        return $ResolvableFactory;
    }
    /**
     * @depends testCanCreateResolvableFactory
     *
     * @param ResolvableFactory $ResolvableFactory
     */
    public function testCanBuildStringResolvable($ResolvableFactory) {
        $StringResolvable = $ResolvableFactory->build("test");
        $this->assertInstanceOf(StringResolvable::class, $StringResolvable);
    }
    
    public function genericSubjectProvider() {
        return [
            [ "test" ],
            [ 1 ],
            [ null ],
            [ [] ],
        ];
    }
    
    public function testCanCoerce() {
        $ResFact   = ResolvableFactory::init();
        $ResFact_3 = [];
        $this->assertEquals($ResFact, ResolvableFactory::init($ResFact));
        $this->assertInstanceOf(ResolvableFactory::class, ResolvableFactory::init($ResFact_3));
    }
    
    /**
     * @dataProvider genericSubjectProvider
     * @depends      testCanCreateResolvableFactory
     *
     * @param                   $subject
     * @param ResolvableFactory $ResolvableFactory
     */
    public function testCanBuildNativeResolvable($subject, $ResolvableFactory) {
        $NativeResolvable = $ResolvableFactory->build($subject);
        $this->assertInstanceOf(NativeResolvable::class, $NativeResolvable);
    }
}
