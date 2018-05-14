<?php


namespace Sm\Data\Evaluation\Validation;


abstract class ValidationResult implements \JsonSerializable {
    /** @var bool */
    protected $success;
    /** @var null|string */
    protected $error;
    public function __construct($success = false, $error = null) {
        $this->success = $success;
        $this->error   = $error;
    }
    
    public function jsonSerialize() {
        return [
            'success' => $this->success,
            'error'   => $this->error,
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
    public function getError(): ?string {
        return $this->error;
    }
}