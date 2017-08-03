<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 12:00 AM
 */

namespace Sm\Core\Hook;


use Sm\Core\Context\Context;


class HasHooksTraitTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolveHooks() {
        /** @var \Sm\Core\Hook\HookHaver $hookHaver */
        $hookHaver = $this->getMockForTrait(HasHooksTrait::class);
        
        $hookHaver->expects($this->any())
                  ->method('getHookContainer')
                  ->willReturn(HookContainer::init());
        
        $hook = $this->createMock(Hook::class);
        $hook->method('resolve')
             ->willReturnCallback(function (Context $context = null, $arg_1) { return 'here ' . $arg_1; });
        
        $hookHaver->addHook('test', $hook);
        $result = $hookHaver->resolveHook('test', null, 'another');
        $this->assertEquals($result, 'here another');
    }
}
