<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 11:23 AM
 */

namespace Sm\Communication\Routing\Module;


use Sm\Communication\CommunicationLayer;
use Sm\Communication\Network\Http\Request\HttpRequest;
use Sm\Core\Resolvable\StringResolvable;

class RoutingModuleTest extends \PHPUnit_Framework_TestCase {
    /** @var  CommunicationLayer $communicationLayer */
    public $communicationLayer;
    public function setUp() {
        $routingModule            = new StandardRoutingModule;
        $layer                    = new CommunicationLayer;
        $this->communicationLayer = $layer->registerRoutingModule($routingModule);
    }
    public function testCanRegisterRoutes() {
        $this->communicationLayer->registerRoutes([ 'test' => StringResolvable::init(123) ]);
        $resp = $this->communicationLayer->route(HttpRequest::init('test'));
        $this->assertEquals('123', $resp);
    }
}
