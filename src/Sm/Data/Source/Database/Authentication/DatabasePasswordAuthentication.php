<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 11:40 PM
 */

namespace Sm\Data\Source\Database\Authentication;

/**
 * Class DatabasePasswordAuthentication
 *
 * Represents authentication that can be done through a password... might be better as an interface
 *
 * @package Sm\Data\Source\Database\Authentication
 */
abstract class DatabasePasswordAuthentication {
    protected $connection;
    
    /**
     * Is the authentication still valid?
     *
     * @return bool
     */
    public function isValid(): bool {
        return isset($this->connection);
    }
}