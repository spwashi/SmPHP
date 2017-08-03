<?php
/**
 * User: Sam Washington
 * Date: 7/25/17
 * Time: 10:00 AM
 */

namespace Sm\Authentication;


use Sm\Core\Factory\StandardFactory;

class AuthenticationFactory extends StandardFactory {
    /**
     * @param object|string $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        return is_a($object_type, Authentication::class);
    }
}