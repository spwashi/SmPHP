<?php
/**
 * User: Sam Washington
 * Date: 7/27/17
 * Time: 7:54 PM
 */

namespace Sm\Modules\Sql\MySql\Module;


use Sm\Core\Module\ModuleProxy;
use Sm\Core\Module\MonitoredModule;
use Sm\Modules\Sql\MySql\Authentication\MySqlAuthentication;

class MySqlQueryModuleProxy extends ModuleProxy implements MonitoredModule {
    /** @var  \Sm\Modules\Sql\MySql\Module\MySqlQueryModule */
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
    public function getMonitors(): array {
        return $this->subject->getMonitors();
    }
}