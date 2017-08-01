<?php
/**
 * User: spwashi2
 * Date: 1/26/2017
 * Time: 3:11 PM
 */

namespace Sm\Core\Resolvable;


class NullResolvable extends NativeResolvable {
    public function setSubject($item = null) {
        $this->subject = null;
        return $this;
    }
    public function resolve($arguments = null) {
        return null;
    }
}