<?php


namespace Sm\Controller;


use ReflectionMethod;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Proxy\Proxy;

class ControllerProxy implements Proxy, Controller {
	/** @var \Sm\Controller\Controller $subject */
	protected $subject;
	#
	public function __construct($subject) {
		$this->subject = $subject;
	}
	/**
	 * @param       $name
	 * @param array $args
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 */
	public function __call($name, $args = []) {
		$check = new ReflectionMethod($this->subject, $name);
		if ($check->isPrivate()) throw new InvalidArgumentException("Cannot access private method");
		return $this->subject->{$name}(...$args);
	}
	public function setLayerRoot(LayerRoot $layerRoot) {
		$this->subject->setLayerRoot($layerRoot);
		return $this;
	}
	public function getLayerRoot(): LayerRoot {
		return $this->subject->getLayerRoot();
	}
	public function proxy(): Controller {
		return $this;
	}
}