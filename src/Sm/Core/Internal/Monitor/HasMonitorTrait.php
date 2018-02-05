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
    /** Set the Monitor that will keep track of this class' inner workings
     *
     * @param string $type
     *
     * @return \Sm\Core\Internal\Monitor\Monitor
     */
    public function getMonitor($type) {
        return $this->getMonitorContainer()->resolve($type);
    }
    protected function __info_monitor__log($name, $details) {
        $monitorContainer = $this->getMonitorContainer();
        
        $monitorContainer->info->append(new GenericEvent($name, $details));
    }
    public function getMonitorContainer(): MonitorContainer {
        if (!isset($this->monitorContainer)) $this->monitorContainer = new MonitorContainer;
        return $this->monitorContainer;
    }
}