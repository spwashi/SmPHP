<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 5:02 PM
 */

namespace Sm\Core\Module;


use Sm\Core\Context\StandardContext;
use Sm\Core\Hook\HookContainer;

class AbstractModuleTest extends \PHPUnit_Framework_TestCase {
    public function testCanInitializeOnContext() {
        /** @var \Sm\Core\Module\StandardModule $abstractModule */
        $abstractModule = $this->getMockForAbstractClass(StandardModule::class);
        $abstractModule->method('getHookContainer')->willReturn($this->createMock(HookContainer::class));
        /** @var \Sm\Core\Context\Context $context */
        $context     = $this->getMockForAbstractClass(StandardContext::class);
        $moduleProxy = $abstractModule->initialize($context);
        $this->assertInstanceOf(ModuleProxy::class, $moduleProxy);
        $this->assertEquals($moduleProxy->getContext(), $context);
    }
    
    public function testCanRunDetach() {
        /** @var \Sm\Core\Module\StandardModule $abstractModule */
        $abstractModule = $this->getMockForAbstractClass(StandardModule::class);
        $abstractModule->method('getHookContainer')->willReturn($this->createMock(HookContainer::class));
        /** @var \Sm\Core\Context\Context $context */
        $context = $this->getMockForAbstractClass(StandardContext::class);
        /** @var \Sm\Core\Context\Context $second_context */
        $second_context = $this->getMockForAbstractClass(StandardContext::class);
        $abstractModule->initialize($context);
        
        # Can deactivate known context
        $result_1 = $abstractModule->deactivate($context);
        $this->assertTrue($result_1);
        $result_2 = $abstractModule->deactivate($context);
        $this->assertNull($result_2);
        # Doesn't do anything on unknown context
        $result_3 = $abstractModule->deactivate($second_context);
        $this->assertNull($result_3);
    }
}
