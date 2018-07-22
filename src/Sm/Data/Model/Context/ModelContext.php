<?php


namespace Sm\Data\Model\Context;


use Sm\Core\Context\Context;
use Sm\Core\SmEntity\Context\SmEntityContext;

abstract class ModelContext implements SmEntityContext {
	/** @var Context describes why we're in this context */
	protected $situational_context;

	public function getSituationalContext(): ?Context {
		return $this->situational_context;
	}
	public function setSituationalContext(Context $situational_context) {
		$this->situational_context = $situational_context;
		return $this;
	}
}