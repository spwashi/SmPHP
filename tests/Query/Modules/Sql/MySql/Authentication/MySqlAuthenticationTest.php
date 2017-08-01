<?php
/**
 * User: Sam Washington
 * Date: 3/13/17
 * Time: 12:09 AM
 */

namespace Sm\Storage\Modules\Sql\MySql;


use Sm\Query\Modules\Sql\MySql\Authentication\MySqlAuthentication;

class MySqlAuthenticationTest extends \PHPUnit_Framework_TestCase {
    /** @var  MySqlAuthentication $MySqlAuthentication */
    protected $MySqlAuthentication;
    public function setUp() {
        $this->MySqlAuthentication = new MySqlAuthentication;
    }
    
    public function testConnection() {
        #todo lol remove this from this file, y'goof!
        $this->MySqlAuthentication->setCredentials("codozsqq", "^bzXfxDc!Dl6", "localhost", "sm_test");
        $this->assertTrue($this->MySqlAuthentication->connect());
    }
}