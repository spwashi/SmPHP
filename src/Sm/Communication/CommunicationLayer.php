<?php
/**
 * User: Sam Washington
 * Date: 6/23/17
 * Time: 2:35 PM
 */

namespace Sm\Communication;


use Sm\Communication\Network\Http\Request\HttpRequestFromEnvironment;
use Sm\Communication\Request\NamedRequest;
use Sm\Communication\Request\RequestFactory;
use Sm\Communication\Response\ResponseDispatcher;
use Sm\Communication\Response\ResponseFactory;
use Sm\Communication\Routing\Module\RoutingModule;
use Sm\Controller\ControllerLayer;
use Sm\Core\Context\Layer\Exception\InaccessibleLayerException;
use Sm\Core\Context\Layer\Module\Exception\MissingModuleException;
use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Module\Error\InvalidModuleException;
use Sm\Core\Module\Module;
use Sm\Core\Module\ModuleContainer;

/**
 * Class CommunicationLayer
 *
 * Layer responsible for inter-service communication.
 * Modules for Routing and Dispatching Requests.
 *
 * @property-read RoutingModule $routing
 */
class CommunicationLayer extends StandardLayer {
    const LAYER_NAME = 'communication';
    
    const ROUTE_RESOLVE_REQUEST = 'ROUTE_RESOLVE_REQUEST';
    const ROUTING_MODULE        = 'routing';
    const HTTP_MODULE           = 'http';
    /** @var \Sm\Communication\Request\RequestFactory Factory used to resolve Requests */
    protected $requestFactory;
    /** @var \Sm\Communication\Response\ResponseFactory Factory used to resolve Responses */
    private $responseFactory;
    /** @var \Sm\Communication\Response\ResponseDispatcher  The thing we use to dispatch Responses */
    private $responseDispatcher;
    /**
     * CommunicationLayer constructor.
     *
     * @param ModuleContainer                                    $moduleContainer
     * @param \Sm\Communication\Request\RequestFactory|null      $requestFactory
     * @param \Sm\Communication\Response\ResponseFactory|null    $responseFactory
     * @param \Sm\Communication\Response\ResponseDispatcher|null $responseDispatcher
     */
    public function __construct(RequestFactory $requestFactory = null,
                                ResponseFactory $responseFactory = null,
                                ResponseDispatcher $responseDispatcher = null) {
        parent::__construct();
        $this->requestFactory     = $requestFactory ?? new RequestFactory;
        $this->responseFactory    = $responseFactory ?? new ResponseFactory;
        $this->responseDispatcher = $responseDispatcher ?? new ResponseDispatcher;
    }
    
    
    ####################################################
    #   Resolvers
    ####################################################
    public function __get($name) {
        if ($name === 'routing') return $this->getRoutingModule();
        # todo bad error
        throw new UnimplementedError("Cannot access this portion of the communication layer");
    }
    public function resolveRequest($name = null):? Request\Request {
        return $this->requestFactory->resolve($name ?? HttpRequestFromEnvironment::class);
    }
    
    ####################################################
    #   Registerers
    ####################################################
    /**
     * Register the Module that is to be used to Route
     *
     * @param \Sm\Communication\Routing\Module\RoutingModule|\Sm\Core\Module\ModuleProxy $routingModule
     *
     * @return  $this
     * */
    public function registerRoutingModule(RoutingModule $routingModule) {
        return $this->registerModule($routingModule, static::ROUTING_MODULE);
    }
    /**
     * Register a bunch of Routes on this Layer.
     *
     * @param array $routes An array indexed by route pattern with resolutions as values.
     *
     * @return mixed
     */
    public function registerRoutes(array $routes) {
        foreach ($routes as $pattern => &$resolution) {
            $this->normalizeResolution($resolution);
        }
        
        
        return $this->getRoutingModule()->registerRoutes($routes);
    }
    /**
     * Register routes in an array, but instead of being indexed by route pattern,
     * they are indexed by route name
     *
     * @param $routes
     *
     * @return mixed
     */
    public function registerNamedRoutes($routes) {
        foreach ($routes as $route_name => &$resolution) {
            $this->normalizeResolution($resolution);
        }
        return $this->getRoutingModule()->registerNamedRoutes($routes);
    }
    
    /**
     * Register a bunch of things that are going to be used to resolve/create requests
     *
     * @param $registry
     */
    public function registerRequestResolvers($registry) {
        $this->requestFactory->register($registry);
    }
    /**
     * Register a bunch of things that are going to be used to resolve/create responses
     *
     * @param $registry
     */
    public function registerResponseResolvers($registry) {
        $this->responseFactory->register($registry);
    }
    /**
     * Register a bunch of handlers that can be used to dispatch responses
     *
     * @param $registry
     */
    public function registerResponseDispatchers($registry) {
        $this->responseDispatcher->register($registry);
    }
    
    ####################################################
    #   Action methods
    ####################################################
    /**
     * Given a request, return the proper response
     *
     * @todo should return Response, accept Request
     *
     * @param $request
     *
     * @return mixed
     * @throws \Sm\Core\Context\Layer\Module\Exception\MissingModuleException
     */
    public function route($request) {
        if ($request === static::ROUTE_RESOLVE_REQUEST) $request = $this->resolveRequest();
        else if (is_string($request)) {
            $request = NamedRequest::init()->setName($request);
        }
        $routingModule = $this->getRoutingModule();
        if (!$routingModule) throw new MissingModuleException("Missing a Routing Module");
        return $routingModule->route($request);
    }
    /**
     * Given a response, send it to where it needs to go.
     *
     * @param string     $type The type of response to return:
     * @param mixed|null $response
     *
     * @return mixed|null
     */
    public function dispatch($type, $response) {
        return $this->responseDispatcher->resolve($type, $response);
    }
    
    ####################################################
    #   Protected Methods
    ####################################################
    /**
     * Get the Module used for Routing
     *
     * @return null|\Sm\Communication\Routing\Module\RoutingModule|\Sm\Core\Module\Module
     * @throws \Sm\Core\Context\Layer\Module\Exception\MissingModuleException
     */
    public function getRoutingModule(): RoutingModule {
        $routingModule = $this->getModule(CommunicationLayer::ROUTING_MODULE);
        if (!$routingModule) throw new MissingModuleException("Missing a Routing Module");
        return $routingModule;
    }
    public function checkCanRegisterModule(Module $module, $name): void {
        $expected_modules = $this->_listExpectedModules();
        if (!in_array($name, $expected_modules)) {
            $st_class = static::class;
            throw new InvalidModuleException("Cannot register module {$name} on layer {$st_class}");
        }
    }
    /**
     * @return array An array of strings corresponding to the names of the modules that this layer needs to have
     */
    protected function _listExpectedModules(): array {
        return [ CommunicationLayer::ROUTING_MODULE, CommunicationLayer::HTTP_MODULE ];
    }
    /**
     * Make sure the resolution that we are assigning to route is in the format that we want it on a layer-to-layer basis.
     * Mostly useful for creating Controller Proxies
     *
     * @param $resolution
     *
     * @throws \Sm\Core\Context\Layer\Exception\InaccessibleLayerException
     */
    private function normalizeResolution(&$resolution): void {
        if (!is_string($resolution)) return;
        $layerRoot = $this->layerRoot;
        if (!isset($layerRoot)) return;
        
        # if there aren't any method-indicating functions, there's nothing to do with the controller
        if (strpos($resolution, '#') === false && strpos($resolution, '@') === false && strpos($resolution, '::') === false) {
            return;
        }
        
        /** @var ControllerLayer $controllerLayer */
        $controllerLayer = $layerRoot->getLayers()->resolve(ControllerLayer::LAYER_NAME);
        
        if (!isset($controllerLayer)) throw new InaccessibleLayerException("No controller available to resolve resolution " . json_encode($resolution));
        
        $resolution = $controllerLayer->createControllerResolvable($resolution);
    }
}