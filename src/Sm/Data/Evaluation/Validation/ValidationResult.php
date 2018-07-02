<?php


namespace Sm\Data\Evaluation\Validation;


abstract class ValidationResult implements \JsonSerializable {
	/** @var bool */
	protected $success;
	/** @var null|string */
	protected $message;
	private   $failed_attributes = [];
	public function __construct($success = false, $error = null) {
		$this->success = $success;
		$this->message = $error;
	}

	public function jsonSerialize() {
		return [
			'success'           => $this->success,
			'message'           => ['_message' => $this->message],
			'failed_attributes' => $this->failed_attributes
		];
	}
	public function isSuccess(): bool { return $this->success; }
	public function getMessage(): ?string { return $this->message; }
	/**
	 * Set the attributes of this Validation that failed via an associative array
	 * (e.g [PropertyValidationResult::ATTR_LENGTH = 'TOO SHORT']
	 * @param array $attributes
	 * @return ValidationResult
	 */
	public function setFailedAttributes(array $attributes) {
		$this->failed_attributes = $attributes;
		return $this;
	}
	public function getFailedAttributes(): array { return $this->failed_attributes; }
}