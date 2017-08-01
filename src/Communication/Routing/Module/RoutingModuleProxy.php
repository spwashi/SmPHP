<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 12:43 PM
 */

namespace Sm\Communication\Routing\Module;


use Sm\Communication\Request\Request;
use Sm\Core\Module\ModuleProxy;

/**
 * Class RoutingModuleProxy
 *
 * Proxy for the Module
 *
 * @package Sm\Communication\Routing\Module
 */
class RoutingModuleProxy extends ModuleProxy implements RoutingModule {
    /** @var  \Sm\Core\Context\Layer\LayerProxy $context */
    protected $context;
    /** @var  \Sm\Communication\Routing\Module\StandardRoutingModule $subject */
    protected $subject;
    public function registerRoutes($routes) {
        return $this->subject->registerRoutes([ $routes ], $this->getContext());
    }
    public function route(Request $request) {
        return $this->subject->route($request, $this->context);
    }
}