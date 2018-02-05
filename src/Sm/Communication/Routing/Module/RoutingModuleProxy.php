<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 12:43 PM
 */

namespace Sm\Communication\Routing\Module;


use Sm\Communication\Request\Request;
use Sm\Communication\Request\RequestDescriptor;
use Sm\Communication\Routing\Route;
use Sm\Core\Module\ModuleProxy;

/**
 * Class RoutingModuleProxy
 *
 * Proxy for the Module
 *
 * @package Sm\Communication\Routing\Module
 */
class RoutingModuleProxy extends ModuleProxy implements RoutingModule {
    /** @var  \Sm\Core\Context\Layer\Layer $context */
    protected $context;
    /** @var  \Sm\Communication\Routing\Module\StandardRoutingModule $subject */
    protected $subject;
    public function registerRoutes($routes) {
        return $this->subject->registerRoutes($routes, $this->getContext());
    }
    public function registerNamedRoutes($routes) {
        return $this->subject->registerNamedRoutes($routes, $this->getContext());
    }
    public function getRoute(Request $request): Route {
        return $this->subject->getRoute($request, $this->getContext());
    }
    /**
     * Describe a Route
     *
     * @param \Sm\Communication\Routing\Route|string $route_or_name Either the route to describe or the
     *
     * @return null|\Sm\Communication\Request\RequestDescriptor
     */
    public function describe($route_or_name):?RequestDescriptor {
        return $this->subject->describe($route_or_name, $this->getContext());
    }
    public function listRoutes(): iterable {
        return $this->subject->listRoutes($this->context);
    }
}