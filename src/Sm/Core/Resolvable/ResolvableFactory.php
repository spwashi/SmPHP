<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 8:24 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Core\Factory\StandardFactory;

class ResolvableFactory extends StandardFactory {
    public static function init($item = null) {
        if ($item instanceof ResolvableFactory) {
            return $item;
        }
        return new static($item);
    }
    public function build($subject = null) {
        if ($subject instanceof Resolvable) {
            return $subject;
        }
        if (!is_callable($subject)) {
            if (is_string($subject)) {
                return new StringResolvable($subject);
            }
            if (!is_object($subject)) {
                return new NativeResolvable($subject);
            }
        } else {
            return new FunctionResolvable($subject);
        }
        return new NativeResolvable($subject);
    }
}