<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:26 PM
 */

namespace Sm\Communication\Routing;


use Sm\Communication\Request\NamedRequest;
use Sm\Communication\Request\Request;
use Sm\Communication\Routing\Event\AddRoute;
use Sm\Communication\Routing\Event\AttemptMatchRoute;
use Sm\Communication\Routing\Exception\RouteNotFoundException;
use Sm\Core\Abstraction\Registry;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Resolvable\Resolvable;

/**
 * Class Router
 *
 * Holds Routes, sending them Requests & arguments from them
 *
 * @package Sm\Communication\Routing\
 */
class Router implements Registry {
    const MONITOR__ROUTE_ATTEMPT_MATCH = 'ROUTE__ATTEMPT_MATCH';
    const MONITOR__ROUTE_ADD           = 'ROUTE__ATTEMPT_ADD';
    /** @var Route[] $routes */
    protected $routes = [];
    use HasMonitorTrait;
    
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
     * @param null        $registrant
     *
     * @return $this
     * @throws \Sm\Communication\Routing\MalformedRouteException
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function register($name = null, $registrant = null) {
        $original = [ $name, $registrant ];
        $addition = AddRoute::init($original);
        
        $this->getMonitor(static::MONITOR__ROUTE_ADD)->append($addition);
        
        if (!isset($registrant)) {
            throw new InvalidArgumentException("Cannot register null routes");
        }
        
        # If we are registering a route from an array
        if (!($registrant instanceof Route)) {
            $resolution = $pattern = null;
            
            if (is_array($registrant)) {
                static::getRouteCreationVariables__array($registrant,
                                                         $name,
                                                         $pattern,
                                                         $resolution);
            } else if ($registrant instanceof Resolvable) {
                $resolution = $registrant;
            }
            $route = Route::init($resolution, $pattern);
        } else {
            $route = $registrant;
        }
        
        # Register it with or without a name
        if (is_string($name)) {
            $this->routes[ $name ] = $route;
        } else {
            $this->routes[] = $route;
        }
        
        $addition->setSuccess(true)->setRoute($route);
        return $this;
    }
    /**
     * Get a route identified by some sort of name
     *
     * @param $name
     *
     * @return null|\Sm\Communication\Routing\Route
     */
    public function getNamed($name): ?Route {
        return $this->routes[ $name ] ?? null;
    }
    /**
     * Resolve a Route based on the name of the route
     *
     * @param $name
     *
     * @return mixed
     */
    public function resolveName(string $name) {
        return $this->resolve(NamedRequest::init()->setName($name));
    }
    public function resolve(Request $request = null): Route {
        if (!$request) {
            throw new UnimplementedError("Can only deal with requests");
        }
        
        /** @var \Sm\Communication\Routing\Route $matching_route */
        if ($request instanceof NamedRequest) {
            $name           = $request->getName();
            $matching_route = $this->getNamed($name);
        } else {
            $matching_route = $this->_getRouteFromRequest($request);
        }
        
        if (isset($matching_route)) {
            return $matching_route;
        }
        
        $json_request           = json_encode($request);
        $routeNotFoundException = new RouteNotFoundException("No matching routes for {$json_request}");
        throw $routeNotFoundException->addMonitors($this->getRelevantMonitors());
    }
    
    protected function _getRouteFromRequest(Request $request): ?Route {
        $matching_route = null;
        foreach ($this->routes as $index => $route) {
            $__does_match      = $route->matches($request);
            $attemptMatchRoute =
                AttemptMatchRoute::init($request, $route);
            
            $this->getMonitorContainer()
                 ->resolve(static::MONITOR__ROUTE_ATTEMPT_MATCH)
                 ->append($attemptMatchRoute);
            
            if ($__does_match) {
                $matching_route = $route;
                $attemptMatchRoute->setSuccess(true);
                break;
            }
            $attemptMatchRoute->setSuccess(false);
        }
        return $matching_route;
    }
    /**
     * @param $registrant
     * @param $name
     * @param $pattern
     * @param $resolution
     *
     * @throws \Sm\Communication\Routing\MalformedRouteException
     */
    private static function getRouteCreationVariables__array($registrant, &$name, &$pattern, &$resolution): void {
# allow us to register routes like [ 'pattern'=> 'resolution']
        if (count($registrant) === 1) {
            $pattern    = key($registrant);
            $resolution = $registrant[ $pattern ];
        } else if (isset($registrant['resolution'])) {
            
            # allow us to register routes like [ 'pattern' => '...', 'resolution' => ..., 'name' => ... ]
            
            if (!isset($registrant['pattern']) && !isset($registrant['name'])) {
                throw new MalformedRouteException("Cannot register a resolution without a name or pattern to go with it");
            }
            
            $name       = $registrant['name'] ?? null;
            $resolution = $registrant['resolution'] ?? null;
            $pattern    = $registrant['pattern'] ?? null;
        }
    }
    /**
     * Get an array of the Monitors to this Router
     *
     * @return array
     */
    public function getRelevantMonitors(): array {
        return [
            static::MONITOR__ROUTE_ATTEMPT_MATCH => $this->getMonitor(static::MONITOR__ROUTE_ATTEMPT_MATCH),
            static::MONITOR__ROUTE_ADD           => $this->getMonitor(static::MONITOR__ROUTE_ADD),
        ];
    }
}