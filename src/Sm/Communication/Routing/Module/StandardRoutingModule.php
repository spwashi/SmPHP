<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 3:12 PM
 */

namespace Sm\Communication\Routing\Module;


use Sm\Communication\Request\Request;
use Sm\Communication\Routing\Route;
use Sm\Communication\Routing\Router;
use Sm\Core\Context\Context;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Context\Layer\Module\LayerModule;
use Sm\Core\Module\ModuleProxy;

class StandardRoutingModule extends LayerModule implements RoutingModule {
    public function registerRoutes($routes, Layer $layerProxy = null) {
        $this->getRouter($layerProxy)->registerBatch($routes);
    }
    public function route(Request $request, Layer $layerProxy = null) {
        return $this->getRouter($layerProxy)->resolve($request);
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