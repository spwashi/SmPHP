<?php
/**
 * User: Sam Washington
 * Date: 6/21/17
 * Time: 11:27 AM
 */

namespace Sm\Core\Module;


use Sm\Core\Abstraction\Registry;
use Sm\Core\Container\Mini\MiniContainer;
use Sm\Core\Context\Context;
use Sm\Core\Context\StandardContext;
use Sm\Core\Hook\HasHooksTrait;
use Sm\Core\Hook\Hook;
use Sm\Core\Hook\HookHaver;

/**
 * Class Module
 *
 * Represents something that we plug in to the framework or application in order to modify the functionality
 * of it a bit more dynamically.
 *
 * One key feature of Modules are the Hooks that tie into them --
 * -- these allow us to modify a Module's functionality essentially on the fly
 *
 * Is this a good thing? Maybe, maybe not. Hopefully hooks and Modules are used responsibly.
 *
 * @package Sm\Core\Module
 */
abstract class StandardModule extends StandardContext implements HookHaver, Module {
    use HasHooksTrait;
    
    /** @var MiniContainer An array of the object_ids of the Contexts this Module has access to. */
    protected $verified_contexts;
    /**
     * AbstractModule constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->verified_contexts = new MiniContainer;
    }
    
    
    /**
     * Get the Module 'primed' in whatever Context we call it.
     * This might mean registering classes or other things.
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return null|\Sm\Core\Module\ModuleProxy
     */
    public function initialize(Context $context = null): ?ModuleProxy {
        # Check to see if we can initialize the Module within this context
        $this->check($context);
        $this->resolveHook(Hook::INIT, $context);
        $this->_initialize($context);
        # Return a ModuleProxy that will allow us to refer to this Module within this Context consistently
        return $this->createModuleProxy($context);
    }
    /**
     * Throw an error if the Context is not valid for whatever reason
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @throws \Sm\Core\Exception\Exception
     * @return bool|null
     */
    public function check(Context $context = null):?bool {
        if ($this->hasValidatedContext($context)) return null;
        
        # This should throw an error if the Module is not applicable on this context
        $this->resolveHook(Hook::CHECK, $context);
        $this->_check($context);
        
        if ($context) $this->createContextRegistry($context);
        return true;
    }
    /**
     * Do the necessary things to remove this Module from the Context it was applied to
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @throws \Sm\Core\Exception\Exception
     * @return bool|null true if successful, null if the context hasn't been validated to begin with, error otherwise
     */
    public function deactivate(Context $context) {
        if (!$this->hasValidatedContext($context)) return null;
        $this->check($context);
        $this->resolveHook(Hook::DEACTIVATE, $context);
        $this->_deactivate($context);
        $this->removeValidatedContext($context);
        return true;
    }
    
    /**
     * @param \Sm\Core\Context\Context $context
     *
     * @return bool
     */
    protected function hasValidatedContext(Context $context = null): bool {
        return $this->verified_contexts->canResolve($context ? $context->getObjectId() : null);
    }
    /**
     * Add it to a list of Contexts we've verified so we know that it's okay (for this Module to act within).
     *
     * Subclasses might want to add some sort of exipiry functionality or something else to modify how Contexts are validated
     *
     * Also, add items that the Context will have registered to it
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return mixed
     */
    protected function createContextRegistry(Context $context): Registry {
        return $this->verified_contexts->register($context->getObjectId(),
                                                  new MiniContainer)
                                       ->resolve($context->getObjectId());
    }
    /**
     * Remove a Context from the "validated contexts" list
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return mixed
     */
    protected function removeValidatedContext(Context $context) {
        return $this->verified_contexts->remove($context->getObjectId());
    }
    /**
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return \Sm\Core\Container\Mini\MiniContainer
     */
    protected function getContextRegistry(Context $context): MiniContainer {
        return $this->verified_contexts->resolve($context->getObjectId());
    }
    
    /**
     * Set up the Class. Meant to be overridden
     *
     * @see \Sm\Core\Module\StandardModule::initialize
     */
    protected function _initialize() { }
    /**
     * Set up the Class. Meant to be overridden
     *
     * @see \Sm\Core\Module\StandardModule::deactivate
     */
    protected function _deactivate() { }
    /**
     * Check to see if the Module is applicable in this context. Meant to be overridden
     *
     * @see \Sm\Core\Module\StandardModule::check
     */
    protected function _check() { }
    
    /**
     * Return a ModuleProxy that maps on to this Module within a given Context
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return \Sm\Core\Module\ModuleProxy
     */
    protected function createModuleProxy(Context $context): ModuleProxy {
        return new ModuleProxy($this, $context);
    }
    
}