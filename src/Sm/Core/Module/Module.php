<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 5:39 PM
 */

namespace Sm\Core\Module;


use Sm\Core\Context\Context;

/**
 * Interface Module
 *
 * For objects that contain grouped functionality in a specific context.
 *
 * Modules should be written to be Context agnostic (though implementations can enforce Context types).
 *
 * Proxies are often used to interact with these objects
 *
 * @package Sm\Core\Module
 */
interface Module extends Context {
    /**
     * Initialize a Module on a Context
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return null|\Sm\Core\Module\ModuleProxy
     * @throws \Sm\Core\Module\Error\InvalidModuleException
     */
    public function initialize(Context $context):?ModuleProxy;
    /**
     * Check to see if a Module can act in a Context
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @throws \Sm\Core\Module\Error\InvalidModuleException
     * @return bool|null
     */
    public function check(Context $context):?bool;
}