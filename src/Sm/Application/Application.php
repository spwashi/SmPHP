<?php


namespace Sm\Application;


use Sm\Communication\CommunicationLayer;
use Sm\Communication\Module\HttpCommunicationModule;
use Sm\Communication\Routing\Module\StandardRoutingModule;
use Sm\Controller\ControllerLayer;
use Sm\Core\Context\Layer\LayerContainer;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Paths\Exception\PathNotFoundException;
use Sm\Core\Util;
use Sm\Data\DataLayer;
use Sm\Query\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Query\Modules\Sql\MySql\Module\MySqlQueryModule;
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
class Application implements LayerRoot {
    use HasObjectIdentityTrait;
    protected $name;
    protected $config_path = 'config/';
    
    /** @var  \Sm\Core\Context\Layer\LayerContainer $layerContainer */
    protected $layerContainer;
    protected $controllerNamespaces = [];
    /** @var string $root_path Where is this application located? */
    private $root_path;
    
    ##################################################
    # Constructors/Initialization
    ##################################################
    /**
     * Application constructor.
     *
     * @param string $name      The name of the application
     * @param string $root_path Where the application is located
     */
    protected function __construct($name, $root_path) {
        $this->name = $name;
        $this->setRootPath($root_path);
        $this->layerContainer = LayerContainer::init();
        $this->initLayers();
    }
    /**
     * @see \Sm\Application\Application::__construct
     *
     * @param $name
     * @param $root_path
     *
     * @return \Sm\Application\Application
     */
    public static function init($name, $root_path) {
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
        $app         = $this;
        $_config_php = $this->root_path . $this->config_path . '_config/_config.php';
        if (file_exists($_config_php)) {
            require_once $_config_php;
        }
    
    
        $_config_json_file = $this->root_path . $this->config_path . 'config.json';
        if (!file_exists($_config_json_file)) throw new PathNotFoundException("Cannot configure app - missing {$_config_json_file}");
        $config_json_str = file_get_contents($_config_json_file);
        #
        $configuration = json_decode($config_json_str, true);
        $this->data->configure($configuration);
    }
    
    
    #
    ##  Layer Management
    protected function initLayers() {
        $this->_registerDataLayer();
        $this->_registerRepresentationLayer();
        $this->_registerControllerLayer();
        $this->_registerCommunicationLayer();
        $this->_registerQueryLayer();
    }
    protected function _registerDataLayer() {
        $this->layerContainer->register(DataLayer::LAYER_NAME, DataLayer::init()->setRoot($this));
    }
    protected function _registerRepresentationLayer() {
        /** @var \Sm\Representation\RepresentationLayer $representationLayer */
        $representationLayer = RepresentationLayer::init()->setRoot($this);
        #------------------------------------------------------------------------
        $this->layerContainer->register(RepresentationLayer::LAYER_NAME, $representationLayer);
    }
    protected function _registerControllerLayer() {
        $this->layerContainer->register(ControllerLayer::LAYER_NAME, (new ControllerLayer)->setRoot($this));
    }
    protected function _registerCommunicationLayer() {
        /** @var CommunicationLayer $communicationLayer $communicationLayer */
        $communicationLayer = CommunicationLayer::init()
                                                ->setRoot($this)
                                                ->registerRoutingModule(new StandardRoutingModule)
                                                ->registerModule(new HttpCommunicationModule, CommunicationLayer::HTTP_MODULE);
        
        #------------------------------------------------------------------------------
        $this->layerContainer->register(CommunicationLayer::LAYER_NAME, $communicationLayer);
    }
    protected function _registerQueryLayer(): QueryLayer {
        $queryLayer = (new QueryLayer)->setRoot($this);
        $this->_registerDefaultQueryModule($queryLayer);
        $this->layerContainer->register(QueryLayer::LAYER_NAME, $queryLayer);
        return $queryLayer;
    }
    protected function _registerDefaultQueryModule(QueryLayer $queryLayer) {
        $module = new MySqlQueryModule;
        $module->registerAuthentication(MySqlAuthentication::init()->setCredentials("codozsqq",
                                                                                    "^bzXfxDc!Dl6",
                                                                                    "localhost",
                                                                                    'sm_test'));
        $queryLayer->registerQueryModule($module, function () use ($module) { return $module; }, false);
    }
    
    ##################################################
    # Getters/Setters
    ##################################################
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
        $this->root_path = rtrim($root_path, '/') . '/';
        return $this;
    }
    public function getLayers(): LayerContainer {
        return $this->layerContainer;
    }
}