<?php
/**
 * User: Sam Washington
 * Date: 6/24/17
 * Time: 7:05 PM
 */

namespace Sm\Core\Hook;

use Sm\Core\Context\Context;


/**
 * interface HookHaver
 *
 * Represents an object that executes various hooks in its lifetime
 *
 * @package Sm\Core\Hook
 */
interface HookHaver {
    /**
     * Register a Hook under the $hooks container to be executed at a certain time in this Module's lifetime
     *
     * @param string                               $hook_name
     * @param \Sm\Core\Resolvable\Resolvable|mixed $hook
     *
     * @return $this
     */
    public function addHook(string $hook_name, Hook $hook);
    /**
     * Get the result of a Hook's execution
     *
     * @param string       $hook_name
     *
     * @param Context|null $context If this Hook is only applicable within a context, pass that Context in here
     *
     * @return mixed|null
     */
    public function resolveHook(string $hook_name, Context $context = null);
}