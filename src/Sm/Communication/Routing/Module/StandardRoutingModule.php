<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 3:12 PM
 */

namespace Sm\Communication\Routing\Module;


use Sm\Communication\Request\Request;
use Sm\Communication\Request\RequestDescriptor;
use Sm\Communication\Routing\Exception\RouteNotFoundException;
use Sm\Communication\Routing\Route;
use Sm\Communication\Routing\Router;
use Sm\Core\Context\Context;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Context\Layer\Module\LayerModule;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Module\ModuleProxy;

class StandardRoutingModule extends LayerModule implements RoutingModule {
    public const INTERNAL = '[internal]';
    public static function init() {
        return new static;
    }
    public function registerNamedRoutes($routes, Layer $layerProxy = null) {
        foreach ($routes as $route_name => $route) {
            $this->getRouter($layerProxy)->register($route_name, $route);
        }
        return $this;
    }
    
    public function registerRoutes($routes, Layer $layerProxy = null) {
        $this->getRouter($layerProxy)->registerBatch($routes);
        return $this;
    }
    /**
     * Describe a route
     *
     * @param                                   $route_or_name
     * @param \Sm\Core\Context\Layer\Layer|null $layerProxy
     *
     * @return \Sm\Communication\Request\RequestDescriptor
     * @throws \Sm\Communication\Routing\Exception\RouteNotFoundException
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function describe($route_or_name, Layer $layerProxy = null): RequestDescriptor {
        if ($route_or_name instanceof Route) $route = $route_or_name;
        else if (is_string($route_or_name)) $route = $this->getRouter($layerProxy)->getNamed($route_or_name);
        else throw new InvalidArgumentException("Can only accept strings or routes");
        
        if (!isset($route)) throw new RouteNotFoundException("Cannot find route with name '{$route_or_name}'");
        
        # Maybe we should check to see if this route is in this router? probably not...
        return $route->getRequestDescriptor();
    }
    public function getRoute(Request $request, Layer $layerProxy = null): Route {
        /** @var Router $router */
        $router = $this->getRouter($layerProxy);
        return $router->resolve($request);
    }
    /**
     * Return the Route belonging to a certain name
     *
     * @param                                   $name
     * @param \Sm\Core\Context\Layer\Layer|null $layerProxy
     *
     * @return Route
     */
    public function routeNamed($name, Layer $layerProxy = null) {
        /** @var Router $router */
        $router = $this->getRouter($layerProxy);
        return $router->resolveName($name);
    }
    protected function _initialize(Layer $layer = null) {
        $this->setRouter($layer, new Router);
        $route = new Route(function () { var_dump('HTHTHT'); }, 'Smd');
    
        $this->registerRoutes([ 't' => $route ], $layer);
    }
    /**
     * @param \Sm\Core\Context\Context $context
     *
     * @return \Sm\Core\Module\ModuleProxy
     */
    protected function createModuleProxy(Context $context = null): ModuleProxy {
        return new RoutingModuleProxy($this, $context);
    }
    /**
     * Set the Router used by this Routing Module
     *
     * @param Layer  $layer
     * @param Router $router
     *
     * @return $this
     */
    protected function setRouter(Layer $layer, Router $router) {
        $this->getContextRegistry($layer)->register('router', $router);
        return $this;
    }
    /**
     * Get the Router used by this Routing Module
     *
     * @param Layer $layer
     *
     * @return \Sm\Communication\Routing\Router
     */
    protected function getRouter(Layer $layer): Router {
        return $this->getContextRegistry($layer)->resolve('router');
    }
}