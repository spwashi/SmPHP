<?php


namespace Sm\Communication\Routing\Exception;


use Sm\Core\Resolvable\Exception\UnresolvableException;

class RouteDoesNotMatchException extends UnresolvableException {
	protected $attemptedRequest;
	protected $route;
	public function setAttemptedRequest($attemptedRequest) {
		$this->attemptedRequest = $attemptedRequest;
		return $this;
	}
	public function jsonSerialize() {
		$jsonSerialize = parent::jsonSerialize();
		if (!is_array($jsonSerialize)) $jsonSerialize = [$jsonSerialize];
		return $jsonSerialize +
		       [
			       'attempt' => $this->attemptedRequest,
			       'route'   => $this->route,
		       ];
	}
	public function setRoute($route) {
		$this->route = $route;
		return $this;
	}

}