<?php
/**
 * User: Sam Washington
 * Date: 7/27/17
 * Time: 7:54 PM
 */

namespace Sm\Modules\Sql\MySql\Module;


use Sm\Core\Module\ModuleProxy;
use Sm\Modules\Sql\MySql\Authentication\MySqlAuthentication;

class MySqlQueryModuleProxy extends ModuleProxy {
    /** @var  \Sm\Modules\Sql\MySql\Module\MySqlQueryModule */
    protected $subject;
    public function getQueryFormatter() {
        return $this->subject->getQueryFormatter($this->context);
    }
    public function interpret($query, MySqlAuthentication $authentication = null) {
        return $this->subject->interpret($this->context, $query, $authentication);
    }
    public function registerAuthentication(MySqlAuthentication $mySqlAuthentication) {
        $this->subject->registerAuthentication($mySqlAuthentication);
        return $this;
    }
}