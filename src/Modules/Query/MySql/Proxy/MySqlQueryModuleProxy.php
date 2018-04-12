<?php
/**
 * User: Sam Washington
 * Date: 7/27/17
 * Time: 7:54 PM
 */

namespace Sm\Modules\Query\MySql\Proxy;


use Sm\Core\Internal\Monitor\MonitorContainer;
use Sm\Core\Internal\Monitor\Monitored;
use Sm\Core\Module\ModuleProxy;
use Sm\Modules\Query\MySql\Authentication\MySqlAuthentication;

class MySqlQueryModuleProxy extends ModuleProxy implements Monitored {
    /** @var  \Sm\Modules\Query\MySql\MySqlQueryModule */
    protected $subject;
    public function getQueryFormatter() {
        return $this->subject->getQueryFormatter($this->context);
    }
    public function interpret($query, $authentication = null) {
        return $this->subject->interpret($query, $this->context, $authentication);
    }
    public function registerAuthentication(MySqlAuthentication $mySqlAuthentication) {
        $this->subject->registerAuthentication($mySqlAuthentication);
        return $this;
    }
    /**
     * Get the Monitors used by this class to debug stuff
     *
     * @return \Sm\Core\Internal\Monitor\Monitor[]
     */
    public function getMonitorContainer(): MonitorContainer {
        return $this->subject->getMonitorContainer();
    }
}