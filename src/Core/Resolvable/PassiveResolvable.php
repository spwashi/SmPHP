<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 2:31 AM
 */

namespace Sm\Core\Resolvable;



/**
 * Class PassiveResolvable
 *
 * This just returns the first argument of whatever we are talking about.
 * It returns the first one because We don't know what else to do.
 *
 * @package Sm\Core\Resolvable
 */
class PassiveResolvable extends AbstractResolvable {
    
    /**
     * @param null|mixed $arguments ,..
     *
     * @return mixed
     */
    public function resolve($arguments = null) {
        if (is_array($arguments) && count($arguments) === 1) {
            return $arguments[ key($arguments) ];
        }
        return $arguments;
    }
}