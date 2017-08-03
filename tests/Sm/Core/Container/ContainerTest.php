<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:11 PM
 */

namespace Sm\Core\Container;


use Sm\Core\Resolvable\NullResolvable;
use Sm\Core\Resolvable\OnceRunResolvable;
use Sm\Core\Resolvable\Resolvable;

class ContainerTest extends \PHPUnit_Framework_TestCase {
    public function setUp() { ; }
    public function tearDown() { }
    public function testCanCreate() {
        $container = new Container;
        $this->assertInstanceOf(Container::class, $container);
        $container = Container::init();
        $this->assertInstanceOf(Container::class, $container);
        return $container;
    }
    /**
     * @depends      testCanCreate
     * @dataProvider Container_Provider
     *
     * @param \Sm\Core\Container\Container $container
     *
     * @return \Sm\Core\Container\Container
     */
    public function testCanRegister(Container $container) {
        $this->assertTrue(true);
        return $this->_register_default($container);
    }
    /**
     *
     * @return array
     */
    public function Container_Provider() {
        $container = Container::init();
        $this->_register_default($container);
        return [
            'original'  => [ $container ],
            'duplicate' => [ $container->duplicate() ],
        ];
    }
    /**
     * @dataProvider Container_Provider
     *
     * @param Container $container
     */
    public function testCanResolve(Container $container) {
        $string_result = $container->resolve('test_string');
        $this->assertEquals("string", $string_result);
        
        $string_result = $container->resolve('other_test_string');
        $this->assertEquals('This is a thing', $string_result);
        
        $fn_result = $container->resolve('test_fn');
        $this->assertEquals("fn", $fn_result);
        
        $test_arr_1_result = $container->resolve('test_arr_1');
        $this->assertEquals(1, $test_arr_1_result);
        
        $test_arr_2_result = $container->resolve('test_arr_2');
        $this->assertEquals("2", $test_arr_2_result);
        
    }
    /**
     * @depends testCanCreate
     *
     * @param \Sm\Core\Container\Container $container
     *
     * @return \Sm\Core\Container\Container
     */
    public function testCanCopy(Container $container) {
        $test_1_fn = function ($argument) {
            return $argument + 1;
        };
        $test_1    = OnceRunResolvable::init($test_1_fn);
        $container->register('test.1', $test_1);
        $this->assertEquals(3, $container->resolve('test.1', 2));
        $NewContainer = $container->duplicate();
        $this->assertEquals(6, $NewContainer->resolve('test.1', 5));
        return $NewContainer;
    }
    /**
     * @depends  testCanCreate
     *
     * @param \Sm\Core\Container\Container $container
     */
    public function testCanGetAll(Container $container) {
        $this->assertInternalType('array', $container->getAll());
    }
    /**
     * @param \Sm\Core\Container\Container $container
     *
     * @depends  testCanCreate
     */
    public function testCanCheckout(Container $container) {
        $container->register([ 'test'  => 1,
                               'hello' => 'Another',
                               'last'  => function () { return 'fifteen'; },
                             ]);
        
        $test_Resolvable = $container->checkout('test');
        
        $this->assertInstanceOf(Resolvable::class, $test_Resolvable);
        $this->assertNotInstanceOf(NullResolvable::class, $test_Resolvable);
        $resolve = $test_Resolvable->resolve();
        $this->assertEquals(1, $resolve);
        $this->assertNull($test_Resolvable->resolve());
        
        $this->assertTrue($container->checkBackIn($test_Resolvable));
        $this->assertNull($test_Resolvable);
        $this->assertEquals(1, $container->checkout('test')->resolve());
        
        
        $this->assertEquals('Another', $container->checkout('hello')
                                                 ->resolve());
        
        $this->assertEquals('fifteen', $container->checkout('last')
                                                 ->resolve());
        
        
    }
    
    public function testCanIterate() {
        $end       = [];
        $array     = [ 'sam', 'bob', 'jan' ];
        $container = Container::init();
        $container->register($array);
    
        foreach ($container as $index => $item) $end[] = $item->resolve();
        
        $this->assertEquals($array, $end);
    }
    /**
     * @param \Sm\Core\Container\Container $container
     *
     * @return \Sm\Core\Container\Container
     */
    protected function _register_default(Container $container) {
        $container->register('test_string', 'string');
        $container->registerDefaults('test_string', 'This is a thing');
        $container->registerDefaults('other_test_string', 'This is a thing');
        $container->register('test_fn', function () { return 'fn'; });
        $container->register([
                                 'test_arr_1' => 1,
                                 'test_arr_2' => function () { return '2'; },
                             ]);
        return $container;
    }
}
