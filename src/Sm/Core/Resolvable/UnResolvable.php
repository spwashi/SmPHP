<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 2:31 AM
 */

namespace Sm\Core\Resolvable;


use Sm\Core\Resolvable\Error\UnresolvableException;

/**
 * Class UnResolvable
 *
 * Throws an error on resolve
 *
 * @package Sm\Core\Resolvable
 */
class UnResolvable extends AbstractResolvable {
    
    /**
     * @param null $_
     *
     * @return mixed
     * @throws \Sm\Core\Resolvable\Error\UnresolvableException
     * @internal param mixed|null|\Sm\Core\Abstraction\Resolvable\Arguments $arguments ,..
     *
     */
    public function resolve($_ = null) {
        throw new UnresolvableException("Cannot resolve");
    }
}