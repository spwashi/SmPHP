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

class Exception extends \Exception implements Monitored, \JsonSerializable {
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
    
    public function __get($name) {
        switch ($name) {
            case 'vars':
                return get_object_vars($this);
        }
    }
    public function jsonSerialize() {
        $trace = $this->getTrace()[0] ?? [];
        return [
            'message'  => $this->getMessage(),
            'previous' => $this->getPrevious(),
            'trace'    => [
                'file'     => $trace['file'] ?? 0,
                'line'     => $trace['line'] ?? 0,
                'function' => $trace['function'] ?? 0,
                'class'    => $trace['class'] ?? 0,
                'args'     => $trace['args'] ?? 0,
            ],
            'full'     => $this->getTrace(),
            'line'     => $this->line,
            'monitors' => $this->relevant_monitors,
        ];
    }
}