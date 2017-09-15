<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:26 PM
 */

namespace Sm\Communication\Routing;


use Sm\Communication\Request\NamedRequest;
use Sm\Communication\Request\Request;
use Sm\Communication\Routing\Exception\RouteNotFoundException;
use Sm\Core\Abstraction\Registry;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\Resolvable;

/**
 * Class Router
 *
 * Holds Routes, sending them Requests & arguments from them
 *
 * @package Sm\Communication\Routing\
 */
class Router implements Registry {
    /** @var Route[] $routes */
    protected $routes = [];
    /** @var array $resolutionNamespaces The namespaces that we will route controllers with */
    protected $resolutionNamespaces = [];
    public static function init() {
        return new static;
    }
    public function __get($name) {
        return $this->resolve(NamedRequest::init()->setName($name));
    }
    public function getRoutes() {
        return $this->routes;
    }
    /**
     * Register multiple items at once, each with a name
     *
     * @param $batch
     *
     * @return $this
     */
    public function registerBatch(array $batch) {
        foreach ($batch as $route_name => $route_desc) {
            $this->register($route_name, $route_desc);
        }
        return $this;
    }
    
    /**
     * @param string|null $name
     * @param null        $registrand
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function register($name = null, $registrand = null) {
        if (!isset($registrand)) throw new InvalidArgumentException("Cannot register null routes");
        
        # If we are registering a route from an array
        if (!($registrand instanceof Route)) {
            $resolution = $pattern = null;
            
            if (is_array($registrand)) {
                # allow us to register routes like [ 'pattern'=> 'resolution']
                if (count($registrand) === 1) {
                    $pattern    = key($registrand);
                    $resolution = $registrand[ $pattern ];
                }
            } else if ($registrand instanceof Resolvable) {
                $resolution = $registrand;
            }
            $route = Route::init($resolution, $pattern);
        } else {
            $route = $registrand;
        }
        
        # Register it with or without a name
        if (is_string($name)) {
            $this->routes[ $name ] = $route;
        } else {
            $this->routes[] = $route;
        }
        return $this;
    }
    protected function _getRouteFromName($name): ?Route {
        return $this->routes[ $name ] ?? null;
    }
    protected function _getRouteFromRequest(Request $request):?Route {
        $matching_route = null;
        foreach ($this->routes as $index => $route) {
            $__does_match = $route->matches($request);
            if ($__does_match) {
                $matching_route = $route;
                break;
            }
        }
        return $matching_route;
    }
    public function resolve(Request $request = null) {
        if (!$request) {
            throw new UnimplementedError("Can only deal with requests");
        }
        
        /** @var \Sm\Communication\Routing\Route $matching_route */
        if ($request instanceof NamedRequest) {
            $matching_route = $this->_getRouteFromName($request->getName());
        } else {
            $matching_route = $this->_getRouteFromRequest($request);
        }
        
        if (isset($matching_route)) {
            $routeResolution = RequestContext::init($request);
            return $matching_route->resolve($request, $routeResolution);
        }
        
        
        $json_request = json_encode($request);
        throw new RouteNotFoundException("No matching routes for {$json_request}");
    }
}