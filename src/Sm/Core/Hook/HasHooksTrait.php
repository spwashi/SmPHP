<?php
/**
 * User: Sam Washington
 * Date: 6/24/17
 * Time: 7:01 PM
 */

namespace Sm\Core\Hook;

use Sm\Core\Context\Context;

/**
 * Class HasHooksTrait
 *
 * Trait meant to carry the functionality of objects that have Hooks.
 *
 * @package Sm\Core\Hook
 */
trait HasHooksTrait {
    /**
     * Register a Hook under the $hooks container to be executed at a certain time in this Module's lifetime
     *
     * @param string                               $hook_name
     * @param \Sm\Core\Resolvable\Resolvable|mixed $hook
     *
     * @return $this
     */
    public function addHook(string $hook_name, Hook $hook) {
        $this->getHookContainer()->register($hook_name, $hook);
        return $this;
    }
    
    /**
     * Get the result of a Hook's execution
     *
     * @param string                   $hook_name
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return mixed|null
     */
    public function resolveHook(string $hook_name, Context $context = null) {
        return $this->getHookContainer()->resolve(...func_get_args());
    }
    /**
     * Get the Hooks held by this class in a HookContainer
     *
     * note: This is abstract in the trait because I feel weird about having properties used in Traits
     *
     * @return null|\Sm\Core\Hook\HookContainer
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    abstract protected function getHookContainer(): ?HookContainer;
}