<?php
/**
 * User: Sam Washington
 * Date: 6/23/17
 * Time: 2:35 PM
 */

namespace Sm\Communication;


use Sm\Communication\Request\NamedRequest;
use Sm\Communication\Request\RequestFactory;
use Sm\Communication\Response\ResponseDispatcher;
use Sm\Communication\Response\ResponseFactory;
use Sm\Communication\Routing\Event\AddRoute;
use Sm\Communication\Routing\Module\RoutingModule;
use Sm\Communication\Routing\Route;
use Sm\Controller\ControllerLayer;
use Sm\Core\Context\Layer\Exception\InaccessibleLayerException;
use Sm\Core\Context\Layer\Module\Exception\MissingModuleException;
use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\Event\GenericEvent;
use Sm\Core\Exception\Exception;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Module\Error\InvalidModuleException;
use Sm\Core\Module\Module;
use Sm\Core\Module\ModuleContainer;
use Sm\Modules\Network\Http\Request\HttpRequestFromEnvironment;

/**
 * Class CommunicationLayer
 *
 * Layer responsible for inter-service communication.
 * Modules for Routing and Dispatching Requests.
 *
 * @property-read RoutingModule $routing
 */
class CommunicationLayer extends StandardLayer {
    # -- class properties
    const LAYER_NAME = 'communication';
    
    # -- routing --
    const ROUTE_RESOLVE_REQUEST = 'ROUTE_RESOLVE_REQUEST';
    const MONITOR__ADD_ROUTE    = 'MONIOTOR__ADD_ROUTE';
    
    # -- name of the 'routing' module
    const MODULE_ROUTING = 'routing';
    const MODULE_HTTP    = 'http';
    
    
    # -- class properties
    const MONITOR__DISPATCH = 'dispatch';
    /** @var \Sm\Communication\Request\RequestFactory Factory used to resolve Requests */
    protected $requestFactory;
    /** @var \Sm\Communication\Response\ResponseFactory Factory used to resolve Responses */
    private $responseFactory;
    /** @var \Sm\Communication\Response\ResponseDispatcher  The thing we use to dispatch Responses */
    private $dispatcher;
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
        $this->requestFactory  = $requestFactory ?? new RequestFactory;
        $this->responseFactory = $responseFactory ?? new ResponseFactory;
        $this->dispatcher      = $responseDispatcher ?? new ResponseDispatcher;
    }
    
    
    ####################################################
    #   Resolvers
    ####################################################
    public function __get($name) {
        $item = parent::__get($name);
        if (isset($item)) return $name;
        
        if ($name === 'routing') return $this->getRoutingModule();
        
        # todo bad error
        throw new UnimplementedError("Cannot access this portion of the communication layer");
    }
    public function resolveRequest($name = null): ?Request\Request {
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
        return $this->registerModule($routingModule, static::MODULE_ROUTING);
    }
    /**
     * Register a bunch of Routes on this Layer.
     *
     * @param array|string $routes An array indexed by route pattern with resolutions as values.
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function registerRoutes($routes) {
        $this->getMonitor('info')->append(GenericEvent::init('add-routes-batch',
                                                             $routes));
        if (is_string($routes)) {
            $routes = json_decode($routes, 1);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException("Invalid JSON cannot be parsed");
            }
        } else if (!is_array($routes)) {
            throw new InvalidArgumentException("Can only register JSON or arrays");
        }
        
        
        $routes = $this->normalizeRouteArray($routes);
        
        return $this->getRoutingModule()->registerRoutes($routes);
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
        $this->dispatcher->register($registry);
    }
    
    ####################################################
    #   Action methods
    ####################################################
    /**
     * Given a request, return the proper response
     *
     * If the request is a string, this navigates to the Routing Mo
     *
     * @todo should return Response, accept Request
     *
     * @param            $request
     *
     * @param array|null $parameters These are the parameters that we are going to pass into whatever we are using.
     *                               This is only used for NamedRequests right now @ todo
     *
     * @return mixed
     */
    public function getRoute($request, array $parameters = null): Route {
        # If we want to resolve the Request from the Environment, do that here
        if ($request === static::ROUTE_RESOLVE_REQUEST) {
            $request = $this->resolveRequest();
        } else if (is_string($request)) {
            $request = NamedRequest::init($request);
        }
        
        if ($request instanceof NamedRequest && isset($parameters)) $request->setParameters($parameters);
        
        return $this->getRoutingModule()->getRoute($request)->prime($request);
    }
    /**
     * Given a response, send it to where it needs to go.
     *
     * @param string     $type The type of response to return:
     * @param mixed|null $response_or_request
     *
     * @return mixed|null
     */
    public function dispatch($type, ...$response_or_request) {
        if ($response_or_request instanceof NamedRequest) {
            try {
                $response_or_request = $this->describe($response_or_request->getName());
            } catch (Exception $e) {
            
            }
        }
        $this->getMonitor(static::MONITOR__DISPATCH)->append(GenericEvent::init('dispatch route -- ', func_get_args()));
        
        return $this->dispatcher->resolve($type, ...$response_or_request);
    }
    public function describe(string $name) {
        return $this->getRoutingModule()->describe($name);
    }
    
    
    /**
     * Get the Module used for Routing
     *
     * @return null|\Sm\Communication\Routing\Module\RoutingModule|\Sm\Core\Module\Module
     * @throws \Sm\Core\Context\Layer\Module\Exception\MissingModuleException
     */
    public function getRoutingModule(): RoutingModule {
        $routingModule = $this->getModule(CommunicationLayer::MODULE_ROUTING);
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
    
    ####################################################
    #   Protected Methods
    ####################################################
    /**
     * @return array An array of strings corresponding to the names of the modules that this layer needs to have
     */
    protected function _listExpectedModules(): array {
        return [ CommunicationLayer::MODULE_ROUTING, CommunicationLayer::MODULE_HTTP ];
    }
    /**
     * Make sure the resolution that we are assigning to route is in the format that we want it on a layer-to-layer basis.
     * Mostly useful for creating Controller Proxies
     *
     * @param $resolution
     *
     * @throws \Sm\Core\Context\Layer\Exception\InaccessibleLayerException
     */
    private function normalizeResolution(&$resolution, array $group_modification_rules = []): void {
        if (!is_string($resolution)) {
            return;
        }
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
    /**
     *
     * @param array $routes
     * @param array $group_rule An array of rules to apply to the group
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function normalizeRouteArray(array $routes, array $group_rule = []) {
        $monitor = $this->getMonitor(static::MONITOR__ADD_ROUTE);
        
        if (isset($routes['routes'])) {
            $group_rule = $routes;
            $routes     = $group_rule['routes'];
            unset($group_rule['routes']);
            
            return $this->normalizeRouteArray($routes, $group_rule);
        }
        
        
        foreach ($routes as &$resolution) {
            $route_config = [ 'resolution' => $resolution, ];
            if (is_array($resolution) && isset($resolution['resolution'])) {
                $resolution = $this->_normalizeArrayLikeRoute($resolution, $group_rule);
            }
            $route_config['normalized_resolution'] = $resolution;
            $addRoute                              = AddRoute::init($route_config, null);
            
            $monitor->append($addRoute);
        }
        return $routes;
    }
    /**
     * Normalize a route when it is registered like an array
     *
     * @param       $resolution
     *
     * @param array $group_rule
     *
     * @return array
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function _normalizeArrayLikeRoute($resolution, array $group_rule): array {
        if (isset($group_rule['pattern_prefix']) && isset($resolution['pattern']) && is_string($resolution['pattern'])) {
            $pattern_prefix = $group_rule['pattern_prefix'];
            if (!is_string($pattern_prefix)) {
                throw new InvalidArgumentException("Pattern Prefix is not valid");
            }
            $resolution['pattern'] = $pattern_prefix . $resolution['pattern'];
        }
        $http_method = $resolution['http_method'] ?? null;
        if (!is_array($http_method)) {
            $resolution['http_method'] = is_string($http_method) ? [ $http_method ] : null;
        }
        if (isset($http_method)) {
            $resolution['pattern'] = [
                'path'        => $resolution['pattern'],
                'http_method' => $resolution['http_method'],
            ];
        }
        
        $this->normalizeResolution($resolution['resolution'], $group_rule);
        return $resolution;
    }
}