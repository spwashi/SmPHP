<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 11:23 AM
 */


use Sm\Communication\CommunicationLayer;
use Sm\Communication\Routing\Exception\RouteNotFoundException;
use Sm\Communication\Routing\Module\StandardRoutingModule;
use Sm\Communication\Routing\Route;
use Sm\Controller\ControllerLayer;
use Sm\Core\Context\Layer\LayerContainer;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Modules\Network\Http\Request\HttpRequest;

class HelloController {
    public function index() {
        return 'hello';
    }
    public function trial() {
        return 'test_trial';
    }
    public function test() {
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
        $resp = $this->communicationLayer->getRoute(HttpRequest::init('test'))->resolve();
        $this->assertEquals('123', $resp);
        $this->expectException(RouteNotFoundException::class);
        $this->communicationLayer->getRoute(HttpRequest::init('test3'));
        
    }
    
    public function testCanRegisterJSON() {
        $controller = new ControllerLayer;
        $layerRoot  = new StdLayerRoot();
        $controller->addControllerNamespace(__NAMESPACE__);
        $layerRoot->getLayers()
                  ->register(ControllerLayer::LAYER_NAME, $controller);
        
        $this->communicationLayer->setRoot($layerRoot);
        $this->communicationLayer->registerRoutes(JSON_CONFIG);
    
        $request = HttpRequest::init('trial');
        $route   = $this->communicationLayer->getRoute($request);
        $resp    = $route->resolve();
        $this->assertEquals('test_trial', $resp);
        $this->expectException(RouteNotFoundException::class);
        $this->communicationLayer->getRoute(HttpRequest::init('test3'));
        
    }
    
    public function testCanResolveNamedRoutes() {
        $this->communicationLayer->registerRoutes([
                                                      [
                                                          'name'       => 'test',
                                                          'resolution' => Route::init(StringResolvable::init('123')),
                                                      ],
                                                  ]);
        $route = $this->communicationLayer->getRoute('test');
        $resp  = $route->resolve();
        $this->assertEquals('123', $resp);
    }
    
    public function testCanResolveDefaultController() {
        $controller = new ControllerLayer;
        $layerRoot  = new StdLayerRoot();
        $controller->addControllerNamespace(__NAMESPACE__);
        $layerRoot->getLayers()->register(ControllerLayer::LAYER_NAME, $controller);
        
        $this->communicationLayer->setRoot($layerRoot);
    
        # Uses HelloController based on @
        $this->communicationLayer->registerRoutes([
                                                      [
                                                          'name'       => 'test',
                                                          'resolution' => 'Hello@test',
                                                      ],
                                                  ]);
        $route = $this->communicationLayer->getRoute('test');
        $resp  = $route->resolve();
        $this->assertEquals('here.test123', $resp);
    }
}
