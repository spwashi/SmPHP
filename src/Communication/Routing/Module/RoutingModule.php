<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 1:15 PM
 */

namespace Sm\Communication\Routing\Module;

use Sm\Communication\Request\Request;


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
     * Given a request, return the result of routing to it
     *
     * @param \Sm\Communication\Request\Request $request
     *
     * @return mixed
     */
    public function route(Request $request);
}