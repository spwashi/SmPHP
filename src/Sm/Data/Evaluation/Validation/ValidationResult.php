<?php


namespace Sm\Data\Evaluation\Validation;


abstract class ValidationResult implements \JsonSerializable {
    /** @var bool */
    protected $success;
    /** @var null|string */
    protected $message;
    public function __construct($success = false, $error = null) {
        $this->success = $success;
        $this->message = $error;
    }
    
    public function jsonSerialize() {
        return [
            'success' => $this->success,
            'message' => $this->message,
        ];
    }
    /**
     * @return bool
     */
    public function isSuccess(): bool {
        return $this->success;
    }
    /**
     * @return null|string
     */
    public function getMessage(): ?string {
        return $this->message;
    }
}