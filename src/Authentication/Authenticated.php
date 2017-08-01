<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 5:49 PM
 */

namespace Sm\Authentication;

/**
 * Interface Authenticated
 *
 * For objects that take Authentication
 *
 * @package Sm\Authentication
 */
interface Authenticated {
    public function isAuthenticated(): bool;
    public function authenticate(Authentication $authentication = null);
}