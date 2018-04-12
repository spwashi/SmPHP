<?php

namespace Sm\Communication;


use Sm\Communication\Routing\Module\StandardRoutingModule;
use Sm\Communication\Routing\RequestContext;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Modules\Network\Http\Request\HttpRequestDescriptor;

class CommunicationLayerTest extends \PHPUnit_Framework_TestCase {
    public function testCanDescribeRoutes() {
        $routes = [
            [ 'test' => FunctionResolvable::init(function (RequestContext $requestContext) { return 'test'; }) ],
            
            [
                'name'       => 'hello',
                'pattern'    => 'wont/you/dance/{preposition}/me',
                'resolution' => FunctionResolvable::init(function (RequestContext $requestContext) { return 'hello'; }),
            ],
        ];
        
        $communicationLayer = CommunicationLayer::init()->registerRoutingModule(StandardRoutingModule::init());
        $communicationLayer->registerRoutes($routes);
        /** @var HttpRequestDescriptor $describeHello */
        $describeHello = $communicationLayer->describe('hello');
        $this->assertInstanceOf(HttpRequestDescriptor::class, $describeHello);
        $as_path = $describeHello->asUrlPath([ 'preposition' => 'on' ]);
        $this->assertEquals('/wont/you/dance/on/me', $as_path);
    }
}
