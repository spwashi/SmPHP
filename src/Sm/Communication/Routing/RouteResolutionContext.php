<?php


namespace Sm\Communication\Routing;


use Sm\Communication\Request\Request;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Context\StandardContext;

/**
 * Class RouteResolutionContext
 *
 * Describes the situation that led to a route being reached.
 */
class RouteResolutionContext extends StandardContext {
    /** @var  \Sm\Core\Context\Layer\LayerRoot $layerRoot */
    protected $layerRoot;
    /** @var  \Sm\Communication\Request\Request $request */
    protected $request;
    public function __construct(Request $request = null, LayerRoot $layerRoot = null) {
        parent::__construct();
    }
    public static function init(Request $request = null, LayerRoot $layerRoot = null) {
        return new static($request, $layerRoot);
    }
    
    public function getLayerRoot(): LayerRoot {
        return $this->layerRoot;
    }
    public function getRequest(): Request {
        return $this->request;
    }
}