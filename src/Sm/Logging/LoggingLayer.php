<?php


namespace Sm\Logging;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sm\Core\Context\Layer\StandardLayer;

class LoggingLayer extends StandardLayer implements \Sm\Logging\Logger {
	const LAYER_NAME = 'logging';
	protected $loggers = [];
	/** @var string $log_path */
	protected $log_path;
	/**
	 * @param $item
	 * @return bool
	 */
	public static function convertThrowableToLoggable($item): array {
		$is_sm              = $item instanceof \Sm\Core\Exception\Exception;
		$exception_messages = [
			'success'   => false,
			'type'      => get_class($item),
			'message'   => $item->getMessage(),
			'file'      => $item->getFile(),
			'line'      => $item->getLine(),
			'trace'     => \Sm\Core\Exception\Exception::getAbbreviatedTrace($item->getTrace()),
			'monitors'  => $is_sm ? $item->getMonitorContainer() : null,
			'previous'  => $item->getPrevious(),
			'exception' => $item,
		];
		return $exception_messages;
	}
	public function __get($name) {
		if (isset($this->loggers[$name])) return $this->loggers[$name];
		return parent::__get($name);
	}
	public function log($item, string $name = null, $level = null) {
		$level = !empty($level) ? $level : Logger::INFO;

		if ($item instanceof \Throwable) {
			$item = self::convertThrowableToLoggable($item);
			$name = $name ?? ($item instanceof \Sm\Core\Exception\Exception ? 'sm_exception' : 'exception');
		}

		$name   = $name ?? 'info';
		$logger = $this->loggers[$name] ?? $this->createLogger($name);
		return $logger->log($level, is_string($item) ? $item : json_encode($item));
	}
	public function createLogger(string $name, $level = null) {
		$level = !empty($level) ? $level : Logger::INFO;

		if (isset($this->loggers[$name])) return $this->loggers[$name];
		if (strpos($name, '/')) {
			$dir_arr = explode('/', $name);
			if (count($dir_arr) !== 1) {
				array_pop($dir_arr);
			}
			$directory = $this->log_path . implode('/', $dir_arr);

			if (!is_dir($directory)) {
				mkdir($directory, 0777, true);
			}
		}
		$log       = new Logger($name);
		$handler   = new StreamHandler($this->log_path . $name . '.log', $level, true, 0774);
		$formatter = new LineFormatter("++[%datetime%] %channel%.%level_name%:\n\t%message%\n");
		$handler->setFormatter($formatter);
		$log->pushHandler($handler);
		$this->loggers[$name] = $log;
		return $log;
	}
	public function setLogPath(string $log_path) {
		$this->log_path = $log_path;
		return $this;
	}
}