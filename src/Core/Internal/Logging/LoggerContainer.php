<?php
/**
 * User: Sam Washington
 * Date: 3/1/17
 * Time: 9:33 PM
 */

namespace Sm\Core\Internal\Logging;

use Sm\Core\Container\Container;

/**
 * Class LoggerContainer
 *
 * Container for Loggers. Meant to be used with Monolog
 *
 * @property
 * @package Sm\Logger
 */
class LoggerContainer extends Container {
    /**
     * @param null|string|null $name
     *
     * @return mixed|null|\Sm\Core\Resolvable\Resolvable
     */
    public function resolve($name = null) {
        return parent::resolve($name);
    }
}