<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:19 PM
 */

namespace Sm\Core\Exception;


use Sm\Core\Internal\Monitor\Monitor;
use Sm\Core\Internal\Monitor\MonitorContainer;
use Sm\Core\Internal\Monitor\Monitored;

class Exception extends \Exception implements Monitored {
    /** @var \Sm\Core\Internal\Monitor\MonitorContainer $relevant_monitors */
    protected $relevant_monitors;
    public function __construct($message = "", $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->relevant_monitors = $this->relevant_monitors ?? new MonitorContainer;
    }
    
    public function addMonitor(string $name, Monitor $monitor) {
        $this->relevant_monitors->register($name, $monitor);
        return $this;
    }
    
    public function addMonitors(array $monitors) {
        foreach ($monitors as $key => $monitor) {
            $this->addMonitor($key, $monitor);
        }
        return $this;
    }
    
    public function getMonitorContainer(): MonitorContainer {
        return $this->relevant_monitors;
    }
}