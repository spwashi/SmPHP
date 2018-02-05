<?php


namespace Sm\Application;


use Sm\Communication\CommunicationLayer;
use Sm\Communication\Module\HttpCommunicationModule;
use Sm\Communication\Routing\Module\StandardRoutingModule;
use Sm\Controller\ControllerLayer;
use Sm\Core\Context\Layer\LayerContainer;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Event\GenericEvent;
use Sm\Core\Exception\Exception;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Internal\Monitor\MonitorContainer;
use Sm\Core\Internal\Monitor\Monitored;
use Sm\Core\Paths\Exception\PathNotFoundException;
use Sm\Core\Util;
use Sm\Data\DataLayer;
use Sm\Query\Module\QueryModule;
use Sm\Query\QueryLayer;
use Sm\Representation\RepresentationLayer;

/**
 * Class Application
 *
 * @property-read CommunicationLayer  $communication
 * @property-read ControllerLayer     $controller
 * @property-read DataLayer           $data
 * @property-read QueryLayer          $query
 * @property-read RepresentationLayer $representation
 * @property-read string              $path
 */
class Application implements \JsonSerializable, LayerRoot {
    use HasObjectIdentityTrait;
    use HasMonitorTrait;
    
    # -- the application name
    protected $name;
    
    # -- paths
    protected $config_path;
    /** @var string $root_path Where is this application located? */
    protected $root_path;
    
    # -- class properties
    /** @var  \Sm\Core\Context\Layer\LayerContainer $layerContainer */
    protected $layerContainer;
    /** @var  \Sm\Core\Internal\Monitor\MonitorContainer[] */
    protected $monitorContainers = [];
    /** @var  \Sm\Application\AppSettings $settings */
    protected $settings;
    
    ##########################################################################
    # Constructors/Initialization
    ##########################################################################
    /**
     * Application constructor.
     *
     * @param string $name        The name of the application
     * @param string $root_path   Where the application is located
     * @param null   $config_path Where all of the config info is
     */
    protected function __construct($root_path, $config_path = null) {
        $this->initSettings()
             ->setRootPath($root_path);
        $this->config_path = $config_path ?? ($this->root_path . 'config/');
        $this->initLayers();
    }
    /**
     * @see \Sm\Application\Application::__construct
     *
     * @param      $name
     * @param      $root_path
     * @param null $config_path
     *
     * @return Application
     */
    public static function init($root_path, $config_path = null) {
        return new static(...func_get_args());
    }
    public function boot(): Application {
        $root_path = $this->root_path;
        if (!file_exists($root_path)) {
            $_rt_path_str = Util::canBeString($root_path) ? $root_path : json_encode($root_path);
            throw new PathNotFoundException("Cannot find path for {$_rt_path_str}");
        }
        
        $this->_configure();
        
        return $this;
    }
    
    #
    ##  Configuration
    /**
     * Configure the Application using the path set on it based on the root path.
     *
     * @throws \Sm\Core\Paths\Exception\PathNotFoundException
     */
    protected function _configure() {
        $_config_php = $this->config_path . 'config.php';
        
        if (file_exists($_config_php)) {
            $app = $this; # defined for the include
            require_once $_config_php;
            $configEvent = GenericEvent::init("Configured application with path " . $_config_php);
        } else {
            $configEvent = GenericEvent::init("Could not configure application with path " . $_config_php);
        }
        $this->getMonitor('info')->append($configEvent);
    }
    
    
    #
    ##  Layer Management
    protected function initLayers() {
        $this->layerContainer = LayerContainer::init();
        
        $this->_registerDataLayer();
        $this->_registerRepresentationLayer();
        $this->_registerControllerLayer();
        $this->_registerCommunicationLayer();
        $this->_registerQueryLayer();
    }
    protected function _registerDataLayer() {
        $dataLayer = DataLayer::init()->setRoot($this);
        $this->layerContainer->register(DataLayer::LAYER_NAME, $dataLayer);
        #--
    
        $this->addMonitoredItem($dataLayer, DataLayer::LAYER_NAME);
    }
    protected function _registerRepresentationLayer() {
        /** @var \Sm\Representation\RepresentationLayer $representationLayer */
        $representationLayer = RepresentationLayer::init()->setRoot($this);
        #------------------------------------------------------------------------
        $this->layerContainer->register(RepresentationLayer::LAYER_NAME, $representationLayer);
        #--
    
        $this->addMonitoredItem($representationLayer, RepresentationLayer::LAYER_NAME);
    }
    protected function _registerControllerLayer() {
        $controllerLayer = (new ControllerLayer)->setRoot($this);
        $this->layerContainer->register(ControllerLayer::LAYER_NAME, $controllerLayer);
        #--
    
        $this->addMonitoredItem($controllerLayer, ControllerLayer::LAYER_NAME);
    }
    protected function _registerCommunicationLayer() {
        /** @var CommunicationLayer $communicationLayer $communicationLayer */
        $communicationLayer = CommunicationLayer::init()
                                                ->setRoot($this)
                                                ->registerRoutingModule(new StandardRoutingModule)
                                                ->registerModule(new HttpCommunicationModule, CommunicationLayer::MODULE_HTTP);
        
        #------------------------------------------------------------------------------
        $this->layerContainer->register(CommunicationLayer::LAYER_NAME, $communicationLayer);
    
        #--
    
        $this->addMonitoredItem($communicationLayer, CommunicationLayer::LAYER_NAME);
    }
    protected function _registerQueryLayer(): QueryLayer {
        $queryLayer = (new QueryLayer)->setRoot($this);
        $this->layerContainer->register(QueryLayer::LAYER_NAME, $queryLayer);
    
    
        #--
    
        $this->addMonitoredItem($queryLayer, QueryLayer::LAYER_NAME);
        
        return $queryLayer;
    }
    public function registerDefaultQueryModule(QueryModule $queryModule) {
        /** @var QueryLayer $queryLayer */
        $queryLayer = $this->layerContainer->resolve(QueryLayer::LAYER_NAME);
        if (!isset($queryLayer)) throw new Exception("Can't register QueryModule without a QueryLayer");
        $queryLayer->registerDefaultQueryModule($queryModule);
    
        $this->addMonitoredItem($queryModule, QueryModule::MONITOR__QUERY_MODULE);
    }
    
    ##########################################################################
    # Getters/Setters
    ##########################################################################
    public function __get(string $name) {
        switch ($name) {
            case CommunicationLayer::LAYER_NAME:
            case QueryLayer::LAYER_NAME:
            case ControllerLayer::LAYER_NAME:
            case DataLayer::LAYER_NAME:
            case RepresentationLayer::LAYER_NAME:
                return $this->layerContainer->resolve($name);
            case'path':
                return $this->root_path;
        }
        throw new InvalidArgumentException("Cannot resolve {$name}");
    }
    protected function setRootPath(string $root_path) {
        $this->root_path      = rtrim($root_path, '/') . '/';
        $this->settings->path = $root_path;
        return $this;
    }
    public function getLayers(): LayerContainer {
        return $this->layerContainer;
    }
    
    /**
     * Initialize the Settings of this Application
     *
     * @return $this
     */
    protected function initSettings() {
        $this->settings           = new AppSettings;
        $this->settings->path     = '';
        $this->settings->name     = '';
        $this->settings->base_url = '';
        
        return $this;
    }
    protected function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function __debugInfo() {
        return [
            'monitors' => $this->getMonitorContainer(),
            'settings' => $this->settings->getAll(),
            'layers'   => $this->layerContainer->getAll(),
        ];
    }
    public function jsonSerialize() {
        return $this->__debugInfo();
    }
    
    public function getMonitors(): MonitorContainer {
        $returnMonitorContainer = new MonitorContainer();
        foreach ($this->monitorContainers as $key => $monitorContainer) {
            $all = $monitorContainer->getAll();
            foreach ($all as $monitor_name => $monitor) {
                $returnMonitorContainer->register($key . '--' . $monitor_name, $monitor);
            }
        }
        return $returnMonitorContainer;
    }
    /**
     * @param \Sm\Core\Internal\Monitor\Monitored|\Sm\Query\Module\QueryModule $queryModule
     * @param string                                                           $name
     */
    protected function addMonitoredItem(Monitored $queryModule, string $name): void {
        $monitors                         = $queryModule->getMonitorContainer();
        $this->monitorContainers[ $name ] = $monitors;
    }
}