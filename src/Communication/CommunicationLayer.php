<?php
/**
 * User: Sam Washington
 * Date: 6/23/17
 * Time: 2:35 PM
 */

namespace Sm\Communication;


use Sm\Communication\Network\Http\Request\HttpRequestFromEnvironment;
use Sm\Communication\Request\RequestFactory;
use Sm\Communication\Response\ResponseDispatcher;
use Sm\Communication\Response\ResponseFactory;
use Sm\Communication\Routing\Module\RoutingModule;
use Sm\Core\Context\Layer\Module\Exception\MissingModuleException;
use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\Module\ModuleContainer;

/**
 * Class CommunicationLayer
 *
 * Layer responsible for inter-service communication.
 * Modules for Routing and Dispatching Requests.
 *
 * @package Sm\Communication
 */
class CommunicationLayer extends StandardLayer {
    const ROUTING_MODULE = 'routing';
    const HTTP_MODULE    = 'http';
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
    public function __construct(ModuleContainer $moduleContainer = null,
                                RequestFactory $requestFactory = null,
                                ResponseFactory $responseFactory = null,
                                ResponseDispatcher $responseDispatcher = null) {
        parent::__construct($moduleContainer);
        $this->requestFactory     = $requestFactory ?? new RequestFactory;
        $this->responseFactory    = $responseFactory ?? new ResponseFactory;
        $this->responseDispatcher = $responseDispatcher ?? new ResponseDispatcher;
    }
    
    
    ####################################################
    #   Resolvers
    ####################################################
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
     * @return  static
     * */
    public function registerRoutingModule(RoutingModule $routingModule) {
        return $this->registerModule(static::ROUTING_MODULE, $routingModule);
    }
    /**
     * Register a bunch of Routes on this Layer
     *
     * @param $routes
     *
     * @return mixed
     */
    public function registerRoutes($routes) {
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
    protected function getRoutingModule(): RoutingModule {
        $routingModule = $this->getModule(CommunicationLayer::ROUTING_MODULE);
        if (!$routingModule) throw new MissingModuleException("Missing a Routing Module");
        return $routingModule;
    }
    /**
     * @return array An array of strings corresponding to the names of the modules that this layer needs to have
     */
    protected function _listExpectedModules(): array {
        return [ CommunicationLayer::ROUTING_MODULE, CommunicationLayer::HTTP_MODULE ];
    }
}