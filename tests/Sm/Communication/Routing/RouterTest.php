<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:27 PM
 */

namespace Sm\Communication\Routing;


use Sm\Communication\Network\Http\Request\HttpRequest;
use Sm\Communication\Request\NamedRequest;
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
     * @param \Sm\Communication\Routing\Router $router
     */
    public function testCanRegister(Router $router) {
        $route_config = [
            Route::init(StringResolvable::init('hello'), 'hello/1'),
            [ 'api/(?:sections|dimensions|collections)' => StringResolvable::init('API example'), ],
            [ 'test' => 'TEST' ],
            [ '$' => function () { return 'Nothing'; },
            ],
            [ '11' => FunctionResolvable::init('\\' . Example::class . '::returnEleven'), ],
        ];
        $router->registerBatch($route_config);
        $this->assertTrue(Route::init(NullResolvable::init(), 'hello/1')->matches(HttpRequest::init('hello/1')));
        
        $hello_1__request      = HttpRequest::init('hello/1');
        $eleven__request       = HttpRequest::init('11');
        $api_sections__request = HttpRequest::init('api/sections');
        
        $this->assertEquals('hello',
                            $router->resolve($hello_1__request)
                                   ->prime($hello_1__request)
                                   ->resolve());
        $this->assertEquals('eleven', $router->resolve($eleven__request)
                                             ->prime($eleven__request)
                                             ->resolve());
        $this->assertEquals('API example', $router->resolve($api_sections__request)
                                                  ->prime($api_sections__request)
                                                  ->resolve());
    }
    
    
    /**
     * @depends testCanCreate
     *
     * @param \Sm\Communication\Routing\Router $router
     */
    public function testCanResolveNamedRrequests(Router $router) {
        $router->register('test', Route::init(StringResolvable::init('TEST')));
        $request    = NamedRequest::init()->setName('test');
        $resolution = $router->resolve($request)
                             ->prime($request)
                             ->resolve();
        $this->assertEquals($resolution, 'TEST');
    }
}
