<?php


namespace Sm\Controller\Event;


use Sm\Core\Event\Event;

/**
 * Class AttemptResolveControllerClass
 *
 * Meant to keep track of if we are successfully resolving the controller class
 */
class Attempt_ResolveControllerClass extends Event {
	private $classname;
	private $success;
	public function __construct($classname, $success = false) {
		parent::__construct();

		$this->classname = $classname;
		$this->success   = $success;
	}
	public static function init($classname, $success = false) {
		return new static($classname, $success);
	}
	/**
	 * Set the "success" flag to true
	 *
	 * @return $this
	 */
	public function declareSuccessful() {
		$this->success = 'declared later';
		return $this;
	}
	public function jsonSerialize() {
		return parent::jsonSerialize() + [
				'success'   => $this->success,
				'classname' => $this->classname,
			];
	}
}