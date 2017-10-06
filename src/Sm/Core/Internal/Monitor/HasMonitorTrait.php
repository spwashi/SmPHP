<?php


namespace Sm\Core\Internal\Monitor;

use Sm\Core\Event\GenericEvent;

/**
 * Trait HasMonitorTrait
 * For classes that keep track of their internal events
 */
trait HasMonitorTrait {
    /** @var  History $_monitor Keep track of things that happen */
    protected $_monitors = [];
    /** Set the Monitor that will keep track of this class' inner workings
     *
     * @param string $type
     *
     * @return \Sm\Core\Internal\Monitor\History
     */
    public function getMonitor($type = History::NOTE) {
        $monitor = $this->_monitors[ $type ] = $this->_monitors[ $type ] ?? new History;
        return $monitor;
    }
    protected function noteEvent($name, $details) {
        $this->getMonitor()->append(new GenericEvent($name, $details));
    }
}