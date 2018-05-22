<?php


namespace Sm\Data\Model\Validation;


use Sm\Data\Evaluation\Validation\ValidationResult;

class ModelValidationResult extends ValidationResult {
    /** @var \Sm\Data\Property\Validation\PropertyValidationResult[] */
    private $propertyValidationResults;
    public function __construct($success = false, $error = null, $propertyValidationResults = []) {
        parent::__construct($success, $error);
        $this->propertyValidationResults = $propertyValidationResults;
    }
    public function jsonSerialize() {
        return parent::jsonSerialize() +
               [
                   'properties' => $this->getPropertyValidationResults(),
               ];
    }
    public function getPropertyValidationResults() {
        return $this->propertyValidationResults;
    }
}