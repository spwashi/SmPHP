<?php
/**
 * User: Sam Washington
 * Date: 1/28/17
 * Time: 11:56 AM
 */

namespace Sm\Communication\Routing;


use Sm\Core\Resolvable\PassiveResolvable;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Modules\Network\Http\Request\HttpRequest;


class Controller {
    public function index() {
        return "HELLO";
    }
}


class RouteTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Route = new Route(null, 'test');
        $this->assertInstanceOf(Route::class, $Route);
        return $Route;
    }
    
    public function testCanMatch() {
        $Route = new Route(null, 'test');
        $this->assertTrue($Route->matches(HttpRequest::init('test')));
        $this->assertTrue($Route->matches(HttpRequest::init('test/')));
        
        $Route = new Route(null, 'api/[a-zA-Z_\d]*');
        $this->assertTrue($Route->matches(HttpRequest::init('api/')));
        $this->assertTrue($Route->matches(HttpRequest::init('api')));
    
        $this->assertFalse($Route->matches(HttpRequest::init('boonman')), 'garbag');
        $this->assertFalse($Route->matches(HttpRequest::init('apis')), 'testing similar regex');
    
        $this->assertTrue($Route->matches(HttpRequest::init('api/Sectf2O_is')));
        $this->assertFalse($Route->matches(HttpRequest::init('api/s*ections')), 'garbage');
        
        $Route = new Route(null, 'api/{test}');
        $this->assertTrue($Route->matches(HttpRequest::init('api/sections')), 'named parameter w no regex');
        
        $Route = new Route(null, '11');
        $this->assertTrue($Route->matches(HttpRequest::init('11')), 'number');
        
        
        $Route = new Route(null, 'api/{test}:[a-zA-Z_\d]*/test/{id}:[\d]*');
        $this->assertTrue($Route->matches(HttpRequest::init('api/sections/test/10/')), 'multiple named parameters');
        
        $Route   = new Route(null, 'api/{test}:[a-zA-Z_\d]*');
        $Request = HttpRequest::init()->setUrl('http://spwashi.com/api/sections');
        $this->assertTrue($Route->matches($Request), 'matching a simple request');
    }
    
    public function testCanResolve() {
        $route = new Route(null, 'api/{test}:[a-zA-Z_\d]*/test/{id}:[\d]*');
        $route->setSubject(PassiveResolvable::init());
        $request = HttpRequest::init('http://spwashi.com/api/pages/test/10');
    
        $route    = Route::init(StringResolvable::init('hello'), '11');
        $request  = HttpRequest::init('11');
        $response = $route->prime($request)->resolve();
        $this->assertEquals('hello', $response);
    }
}
