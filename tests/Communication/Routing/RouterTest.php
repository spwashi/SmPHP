<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:27 PM
 */

namespace Sm\Communication\Routing;


use Sm\Communication\Network\Http\Request\HttpRequest;
use Sm\Communication\Routing\Exception\RouteNotFoundException;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\NullResolvable;
use Sm\Core\Resolvable\StringResolvable;

class Example {
    public function returnEleven() {
        return 'eleven';
    }
}

class RouterTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Router = new Router();
        $this->assertInstanceOf(Router::class, $Router);
        
        $Router = Router::init();
        $this->assertInstanceOf(Router::class, $Router);
        return $Router;
    }
    public function testThrowsErrorOnNoResolution() {
        $router = new Router();
        $this->expectException(RouteNotFoundException::class);
        $router->resolve(HttpRequest::init('test'));
    }
    /**
     * @depends testCanCreate
     *
     * @param \Sm\Communication\Routing\Router $Router
     */
    public function testCanRegister(Router $Router) {
        $route_config = [
            Route::init(StringResolvable::init('hello'), 'hello/1'),
            [ 'api/(?:sections|dimensions|collections)' => StringResolvable::init('API example'), ],
            [ 'test' => 'TEST' ],
            [ '$' => function () { return 'Nothing'; },
            ],
            [ '11' => FunctionResolvable::init('\\' . Example::class . '::returnEleven'), ],
        ];
        $Router->registerBatch($route_config);
        $this->assertTrue(Route::init(NullResolvable::init(), 'hello/1')->matches(HttpRequest::init('hello/1')));
        
        
        $this->assertEquals('hello',
                            $Router->resolve(HttpRequest::init()->setUrl('hello/1')));
        $this->assertEquals('eleven',
                            $Router->resolve(HttpRequest::init()->setUrl('11')));
        $this->assertEquals('API example',
                            $Router->resolve(HttpRequest::init()->setUrl('api/sections')));
    }
}
