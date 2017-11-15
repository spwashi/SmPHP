<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:19 PM
 */

namespace Sm\Core\Exception;


use Sm\Core\Internal\Monitor\Monitor;

class Exception extends \Exception {
    protected $relevant_monitors = [];
    public function addMonitor(string $name, Monitor $monitor) {
        $this->relevant_monitors[ $name ] = $monitor;
        return $this;
    }
    public function addMonitors(array $monitors) {
        foreach ($monitors as $key => $monitor) {
            $this->addMonitor($key, $monitor);
        }
        return $this;
    }
    
    public function getRelevantMonitors(): array {
        return $this->relevant_monitors;
    }
}