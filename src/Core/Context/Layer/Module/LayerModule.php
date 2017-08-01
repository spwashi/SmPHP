<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 7:47 AM
 */

namespace Sm\Core\Context\Layer\Module;

use Sm\Core\Context\Context;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Hook\HookContainer;
use Sm\Core\Module\ModuleProxy;
use Sm\Core\Module\StandardModule;

/**
 * Class LayerModule
 *
 * @package Sm\Core\Context\Layer
 */
abstract class LayerModule extends StandardModule {
    /** @var \Sm\Core\Hook\HookContainer $hookContainer */
    protected $hookContainer;
    
    /**
     * Get the Hooks held by this class in a HookContainer
     *
     * @return null|\Sm\Core\Hook\HookContainer
     */
    protected function getHookContainer(): ?HookContainer {
        return $this->hookContainer = $this->hookContainer ?? new HookContainer;
    }
    protected function createModuleProxy(Context $context = null): ModuleProxy {
        return new LayerModuleProxy($this, $context);
    }
    
    protected function _check(Layer $context = null) { return parent::_check(); }
    protected function _initialize(Layer $context = null) { return parent::_initialize(); }
    protected function _deactivate(Layer $context = null) { return parent::_deactivate(); }
}