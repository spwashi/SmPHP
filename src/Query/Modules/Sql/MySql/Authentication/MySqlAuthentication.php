<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 5:26 PM
 */

namespace Sm\Query\Modules\Sql\MySql\Authentication;

use Sm\Authentication\AbstractAuthentication;
use Sm\Query\Modules\Sql\Authentication\SqlAuthentication;


/**
 * Class MySqlAuthentication
 *
 * Authentication for connecting to a MySql database
 *
 * @package Sm\Query\Modules\Sql\MySql\Authentication
 * @method \PDO getConnection()
 */
class MySqlAuthentication extends AbstractAuthentication implements SqlAuthentication {
    protected $database_name;
    protected $host;
    protected $password;
    protected $username;
    
    public function setCredentials($username = null, $password = null, $host = null, $database = null) {
        if (isset($host)) $this->host = $host;
        if (isset($username)) $this->username = $username;
        if (isset($password)) $this->password = $password;
        if (isset($database)) $this->database_name = $database;
        return $this;
    }
    
    /**
     * Get then Host name that we will use to connect
     *
     * @return string
     */
    public function getHost() {
        return $this->host;
    }
    /**
     * Get the name of the database we're using to connect
     *
     * @return string
     */
    public function getDatabaseName() {
        return $this->database_name;
    }
    
    /**
     * Is the authentication still valid?
     *
     * @return bool
     */
    public function isValid(): bool {
        return isset($this->connection);
    }
    /**
     * Connect to the Authentication using the available credentials
     *
     * @return mixed
     */
    public function connect() {
        $dsn              = "mysql:host=" . $this->host . ";dbname=" . $this->database_name . ';charset=utf8';
        $username         = $this->username;
        $password         = $this->password;
        $this->connection = new \PDO($dsn, $username, $password);
        
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        
        return $this->isValid();
    }
}