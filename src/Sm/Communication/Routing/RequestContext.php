<?php


namespace Sm\Communication\Routing;


use Sm\Communication\Request\Request;
use Sm\Core\Context\StandardContext;

/**
 * Class RouteResolutionContext
 *
 * Describes the situation that led to a route being reached.
 */
class RequestContext extends StandardContext {
    /** @var  \Sm\Communication\Request\Request $request */
    protected $request;
    /** @var  Route $route */
    protected $route;
    public function __construct(Request $request) {
        parent::__construct();
        $this->request = $request;
    }
    public static function init(Request $request) {
        return new static($request);
    }
    
    public function getRequest(): Request {
        return $this->request;
    }
    /**
     * @param Route $route
     *
     * @return $this
     */
    public function setRoute(Route $route) {
        $this->route = $route;
        return $this;
    }
    public function getRoute(): Route {
        return $this->route;
    }
}