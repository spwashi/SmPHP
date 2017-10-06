<?php


namespace Sm\Core\Internal\Monitor;

/**
 * Trait HasMonitorTrait
 * For classes that keep track of their internal events
 */
trait HasMonitorTrait {
    /** @var  Monitor $_monitor Keep track of things that happen */
    protected $_monitors = [];
    /** Set the Monitor that will keep track of this class' inner workings
     *
     * @param string $type
     *
     * @return \Sm\Core\Internal\Monitor\Monitor
     */
    public function getMonitor($type = Monitor::NOTE) {
        $monitor = $this->_monitors[ $type ] = $this->_monitors[ $type ] ?? new Monitor;
        return $monitor;
    }
    protected function noteEvent($name, ...$details) {
        $this->getMonitor()->appendEvent($name, ...$details);
    }
}