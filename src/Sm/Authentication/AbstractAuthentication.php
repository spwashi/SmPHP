<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 11:20 PM
 */

namespace Sm\Authentication;

abstract class AbstractAuthentication implements Authentication {
    protected $connection;
    /**
     * Static constructor
     *
     * @return static
     */
    public static function init() {
        return new static(...func_get_args());
    }
    abstract public function isValid(): bool;
    abstract public function connect();
    abstract public function setCredentials();
    public function getConnection() {
        return $this->connection;
    }
}