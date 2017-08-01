<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:24 PM
 */

namespace Sm\Core\Resolvable;

/**
 * Class NativeResolvable
 *
 * This resolves exactly what we pass in.
 *
 * @package Sm\Core\Resolvable
 */
class NativeResolvable extends AbstractResolvable {
    public function resolve($name = null) {
        return $this->subject;
    }
}