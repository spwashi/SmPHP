<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 7:45 AM
 */

namespace Sm\Core\Module;


use Sm\Core\Context\Context;
use Sm\Core\Context\Proxy\StandardContextualizedProxy;
use Sm\Core\Resolvable\FunctionResolvable;

/**
 * Class ModuleProxy
 *
 * Proxy for Modules
 *
 * @package Sm\Core\Module
 */
class ModuleProxy extends StandardContextualizedProxy {
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
}