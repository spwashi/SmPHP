<?php


namespace Sm\Logging;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sm\Core\Context\Layer\StandardLayer;

class LoggingLayer extends StandardLayer {
    const LAYER_NAME = 'logging';
    protected $loggers = [];
    /** @var string $log_path */
    protected $log_path;
    public function __get($name) {
        if (isset($this->loggers[ $name ])) return $this->loggers[ $name ];
        return parent::__get($name);
    }
    public function log($item, string $name = 'info', $level = Logger::INFO) {
        $logger = $this->loggers[ $name ] ?? $this->createLogger($name);
        return $logger->log($level, is_string($item) ? $item : json_encode($item));
    }
    public function createLogger(string $name, $level = Logger::INFO) {
        if (isset($this->loggers[ $name ])) return $this->loggers[ $name ];
        $log = new Logger($name);
        $log->pushHandler(new StreamHandler($this->log_path . $name . '.log', $level));
        $this->loggers[ $name ] = $log;
        return $log;
    }
    public function setLogPath(string $log_path) {
        $this->log_path = $log_path;
        return $this;
    }
}