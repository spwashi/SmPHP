<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 5:29 PM
 */

namespace Sm\Core\Context\Layer\Module;


use Sm\Core\Context\Layer\Layer;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Context\Layer\StandardLayer;

class LayerModuleTest extends \PHPUnit_Framework_TestCase {
    public function testCanOnlyRegisterLayers() {
        /** @var \Sm\Core\Context\Layer\Module\LayerModule $layerModule */
        $layerModule = $this->getMockForAbstractClass(LayerModule::class);
        /** @var Layer $layer */
        $layer = $this->getMockForAbstractClass(StandardLayer::class);
        /** @var LayerRoot $layerRootMock */
        $layerRootMock = $this->createMock(LayerRoot::class);
        $layerModule->initialize($layer->initialize($layerRootMock));
    }
}
