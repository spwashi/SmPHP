<?php
/**
 * User: Sam Washington
 * Date: 7/25/17
 * Time: 10:01 AM
 */

namespace Sm\Authentication;


/**
 * Class Authentication
 *
 * A class meant to represent a connection to a resource
 *
 * @package Sm\Authentication
 */
interface Authentication {
    /**
     * Is the authentication still valid?
     *
     * @return bool
     */
    public function isValid(): bool;
    /**
     * Connect to the Authentication using the available credentials
     *
     * @return  bool
     */
    public function connect();
}