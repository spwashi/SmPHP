<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 7:47 AM
 */

namespace Sm\Core\Context\Layer\Module;

use Sm\Core\Context\Layer\Layer;
use Sm\Core\Hook\HookContainer;
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
    
    protected function validateContext(Layer $context = null) { return parent::validateContext(); }
    protected function establishContext(Layer $context = null) { return parent::establishContext(); }
    protected function removeFromContext(Layer $context = null) { return parent::removeFromContext(); }
}