<?php


namespace Sm\Data\Property\Context;


use Sm\Core\Context\Context;
use Sm\Core\Context\Proxy\ContextualizedProxy;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Proxy\Proxy;
use Sm\Data\Property\PropertyContainer;

/**
 * Represents a PropertyContainer that exists in a specific context
 */
class PropertyContainerProxy extends PropertyContainer implements ContextualizedProxy {
	public function __construct($subject, Context $context = null) {
		parent::__construct();
		$this->subject = $subject;
		if ($context) $this->setContext($context);
	}
	public function getContext(): Context {
		return $this->context;
	}
	protected function setContext(Context $context) {
		$this->context = $context;
		return $this;
	}
}