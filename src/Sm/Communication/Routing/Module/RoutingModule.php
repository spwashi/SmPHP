<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 1:15 PM
 */

namespace Sm\Communication\Routing\Module;

use Sm\Communication\Request\Request;
use Sm\Communication\Request\RequestDescriptor;


/**
 * Class RoutingModule
 *
 * Module that will be used to execute some functionality based on a Request, usually returning a Response
 *
 * @package Sm\Communication\Routing
 */
interface RoutingModule {
    /**
     * Responsible for Registering routes to this Module
     *
     * @param                                   $routes
     */
    public function registerRoutes($routes);
    /**
     * Register an array of named routes
     *
     * @param $routes
     *
     * @return mixed
     */
    public function registerNamedRoutes($routes);
    /**
     * Given a request, return the result of routing to it
     *
     * @param \Sm\Communication\Request\Request $request
     *
     * @return mixed
     */
    public function route(Request $request);
    /**
     * Describe a Route
     *
     * @param \Sm\Communication\Routing\Route|string $route_or_name Either the route to describe or the
     *
     * @return null|\Sm\Communication\Request\RequestDescriptor
     */
    public function describe($route_or_name):?RequestDescriptor;
}