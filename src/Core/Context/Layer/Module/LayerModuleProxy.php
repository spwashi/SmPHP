<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 3:51 PM
 */

namespace Sm\Core\Context\Layer\Module;

use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\Context\Proxy\ContextualizedProxy;
use Sm\Core\Module\ModuleProxy;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * Class LayerModuleProxy
 *
 * @package Sm\Core\Context\Layer\Module
 * @method StandardLayer getContext()
 */
class LayerModuleProxy extends ModuleProxy implements ContextualizedProxy {
}