<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 5:44 PM
 */

namespace Sm\Core\Context\Proxy;

use Sm\Core\Context\Context;
use Sm\Core\Proxy\Proxy;

/**
 * Interface ContextualizedProxy
 *
 * Interface for Proxies that exist to hold reference to a Context
 *
 * @package Sm\Core\Context
 */
interface ContextualizedProxy extends Proxy {
    /**
     * Get the Context that the Proxy is referencing
     *
     * @return \Sm\Core\Context\Context
     */
    public function getContext(): Context;
}