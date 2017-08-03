<?php
/**
 * User: Sam Washington
 * Date: 2/4/17
 * Time: 10:27 PM
 */

namespace Sm\Core\Paths;

use Sm\Core\Container\Container;
use Sm\Core\Resolvable\Error\UnresolvableException;


/**
 * Class PathContainer
 *
 * Contains paths to directories
 *
 * @package Sm\Application
 */
class PathContainer extends Container {
    /**
     * @param null $name
     *
     * @return null|string
     * @throws \Sm\Core\Resolvable\Error\UnresolvableException
     */
    public function resolve($name = null) {
        # Resolve with the name of the path we want and the PathContainer
        $string = parent::resolve($name, $this);
        if (!is_string($string)) {
            throw new UnresolvableException("Trying to resolve something that will not be a path");
        }
        return rtrim($string, '/') . '/';
    }
}