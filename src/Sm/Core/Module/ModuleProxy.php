<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 7:45 AM
 */

namespace Sm\Core\Module;


use Sm\Core\Context\Context;
use Sm\Core\Context\Proxy\StandardContextualizedProxy;

/**
 * Class ModuleProxy
 *
 * Proxy for Modules
 *
 * @package Sm\Core\Module
 */
class ModuleProxy extends StandardContextualizedProxy implements Module {
    /** @var \Sm\Core\Module\Module $subject The module being proxied */
    protected $subject;
    /**
     * ModuleProxy constructor.
     *
     *
     * @param \Sm\Core\Module\Module   $module
     * @param \Sm\Core\Context\Context $context
     */
    public function __construct(Module $module, Context $context = null) {
        parent::__construct($module, $context);
    }
    public function __call($name, $args) {
        return call_user_func_array([ $this->subject, $name ], $args);
    }
    public function initialize(Context $context) {
        return $this;
    }
    public function check(Context $context):?bool {
        return true;
    }
}