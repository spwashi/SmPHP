<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:26 PM
 */

namespace Sm\Communication\Routing;


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
    public static function init() {
        return new static;
    }
    public function __get($name) {
        return $this->resolve($name);
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
            $registrand = Route::init($resolution, $pattern);
        }
        
        if (is_string($name)) {
            $this->routes[ $name ] = $registrand;
        } else {
            $this->routes[] = $registrand;
        }
        return $this;
    }
    public function resolve(Request $Request = null) {
        if (!$Request) {
            throw new UnimplementedError("Can only deal with requests");
        }
        foreach ($this->routes as $index => $route) {
            $__does_match = $route->matches($Request);
            if ($__does_match) return $route->resolve($Request);
        }
        throw new RouteNotFoundException("No matching routes");
    }
}