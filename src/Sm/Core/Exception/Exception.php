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
        $all_trace_arr    = $this->getTrace();
        $message          = $this->resolveMessage();
        $relevantMonitors = $this->getRelevantMonitors();
        $fullTrace        = $this->getAbbreviatedTrace($all_trace_arr);
        $previous         = $this->getPreviousException();
        $trace            = $all_trace_arr[0] ?? [];
        
        return [
            'message'  => $message,
            'file'     => $this->getFile(),
            'line'     => $this->line,
            'previous' => $previous,
            'trace'    => $fullTrace,
            'monitors' => $relevantMonitors,
        ];
    }
    /**
     * @return \Sm\Core\Internal\Monitor\MonitorContainer|string
     */
    protected function getRelevantMonitors() {
        try {
            $relevantMonitors = $this->relevant_monitors;
        } catch (\Exception $e) {
            $relevantMonitors = 'Unresolvable relevantMonitors';
        }
        return $relevantMonitors;
    }
    /**
     * @return string
     */
    protected function resolveMessage(): string {
        try {
            $message = parent::getMessage();
        } catch (\Exception $e) {
            $message = 'Unresolvable message';
        }
        return $message;
    }
    /**
     * @return \Exception|string
     */
    protected function getPreviousException() {
        try {
            $previous = parent::getPrevious();
        } catch (\Exception $e) {
            $previous = 'Unresolvable previous';
        }
        return $previous;
    }
    /**
     * @param $all_trace_arr
     *
     * @return array|string
     */
    protected function getAbbreviatedTrace($all_trace_arr) {
        try {
            $fullTrace = [];
            foreach ($all_trace_arr as $item) {
                try {
                    $fullTrace[] = json_decode(json_encode($item), 1);
                } catch (\Exception $exception) {
                    $fullTrace[] = [
                        'EXCEPTION_IN_EXCEPTION',
                        [ $item['file'] ?? 0,
                          $item['line'] ?? 0, ],
                        $exception->getMessage(),
                        $exception->getLine(),
                        $exception->getFile(),
                    ];
                }
            }
        } catch (\Exception $e) {
            $fullTrace = 'Unresolvable trace';
        }
        return $fullTrace;
    }
}