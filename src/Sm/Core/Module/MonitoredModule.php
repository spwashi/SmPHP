<?php


namespace Sm\Core\Module;


interface MonitoredModule {
    /**
     * Get the Monitors used by this class to debug stuff
     *
     * @return \Sm\Core\Internal\Monitor\Monitor[]
     */
    public function getMonitors(): array;
}