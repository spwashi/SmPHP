<?php


namespace Sm\Core\Internal\Monitor;


interface Monitored {
    /**
     * Get the Monitors used by this class to debug stuff
     *
     * @return \Sm\Core\Internal\Monitor\Monitor[]
     */
    public function getMonitorContainer(): MonitorContainer;
}