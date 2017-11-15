<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 11:23 AM
 */


use Sm\Communication\CommunicationLayer;
use Sm\Communication\Network\Http\Request\HttpRequest;
use Sm\Communication\Routing\Exception\RouteNotFoundException;
use Sm\Communication\Routing\Module\StandardRoutingModule;
use Sm\Communication\Routing\RequestContext;
use Sm\Communication\Routing\Route;
use Sm\Controller\ControllerLayer;
use Sm\Core\Context\Layer\LayerContainer;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Resolvable\StringResolvable;

class HelloController {
    public function index() {
        return 'hello';
    }
    public function trial() {
        return 'test_trial';
    }
    public function test(RequestContext $r) {
        $thing = $r->getRequest();
        return 'here.test123';
    }
}

class StdLayerRoot implements LayerRoot {
    protected $layerContainer;
    public function getObjectId() {
        return 'test';
    }
    public function getLayers(): LayerContainer {
        return $this->layerContainer = $this->layerContainer ?? LayerContainer::init();
    }
}

const JSON_CONFIG =
'[
    {
        "name":       "rt_404",

        "pattern":    "404/{error}",
        "resolution": "#ErrorController::rt_404"
    },
    {
        "name":       "home",

        "pattern":    "trial",
        "resolution": "Hello@trial"
    },
    {
        "pattern":    "wanghorn/test",
        "resolution": "#Home::item"
    },
    {
        "pattern":    "wanghorn/one",
        "resolution": "#Temp::react_1"
    },
    {
        "pattern":    "wanghorn/dev/models",
        "resolution": "#Dev::modelsToTables"
    }
]';


class RoutingModuleTest extends \PHPUnit_Framework_TestCase {
    /** @var  CommunicationLayer $communicationLayer */
    public $communicationLayer;
    public function setUp() {
        $routingModule            = new StandardRoutingModule;
        $layer                    = new CommunicationLayer;
        $this->communicationLayer = $layer->registerRoutingModule($routingModule);
    }
    public function testCanRegisterRoutes() {
        $this->communicationLayer->registerRoutes([
                                                      [ 'test' => StringResolvable::init(123) ],
                                                  ]);
        $resp = $this->communicationLayer->route(HttpRequest::init('test'));
        $this->assertEquals('123', $resp);
        $this->expectException(RouteNotFoundException::class);
        $this->communicationLayer->route(HttpRequest::init('test3'));
        
    }
    
    public function testCanRegisterJSON() {
        $controller = new ControllerLayer;
        $layerRoot  = new StdLayerRoot();
        $controller->addControllerNamespace(__NAMESPACE__);
        $layerRoot->getLayers()
                  ->register(ControllerLayer::LAYER_NAME, $controller);
        
        $this->communicationLayer->setRoot($layerRoot);
        
        $this->communicationLayer->registerRoutes(JSON_CONFIG);
        $resp = $this->communicationLayer->route(HttpRequest::init('trial'));
        $this->assertEquals('test_trial', $resp);
        $this->expectException(RouteNotFoundException::class);
        $this->communicationLayer->route(HttpRequest::init('test3'));
        
    }
    
    public function testCanResolveNamedRoutes() {
        $this->communicationLayer->registerNamedRoutes([ 'test' => Route::init(StringResolvable::init('123')) ]);
        $resp = $this->communicationLayer->route('test');
        $this->assertEquals('123', $resp);
    }
    
    public function testCanResolveDefaultController() {
        $controller = new ControllerLayer;
        $layerRoot  = new StdLayerRoot();
        $controller->addControllerNamespace(__NAMESPACE__);
        $layerRoot->getLayers()
                  ->register(ControllerLayer::LAYER_NAME, $controller);
        
        $this->communicationLayer->setRoot($layerRoot);
    
        # Uses HelloController based on @
        $this->communicationLayer->registerNamedRoutes([ 'test' => 'Hello@test' ]);
        $resp = $this->communicationLayer->route('test');
        $this->assertEquals('here.test123', $resp);
    }
}
