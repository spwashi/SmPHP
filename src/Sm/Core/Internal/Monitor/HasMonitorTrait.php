<?php


namespace Sm\Core\Internal\Monitor;

use Sm\Core\Event\GenericEvent;

/**
 * Trait HasMonitorTrait
 * For classes that keep track of their internal events
 */
trait HasMonitorTrait {
    /** @var  \Sm\Core\Internal\Monitor\MonitorContainer $monitorContainer Keep track of things that happen */
    protected $monitorContainer;
    
    protected function __info_monitor__log($name, $details) {
        $monitorContainer = $this->getMonitorContainer();
        
        $monitorContainer->info->append(new GenericEvent($name, $details));
    }
    protected function getMonitorContainer(): MonitorContainer {
        if (!isset($this->monitorContainer)) $this->monitorContainer = new MonitorContainer;
        return $this->monitorContainer;
    }
}