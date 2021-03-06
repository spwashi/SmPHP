<?php


namespace Sm\Data\Entity\Validation;


use Sm\Data\Entity\Property\Validation\EntityPropertyValidationResult;
use Sm\Data\Evaluation\Validation\ValidationResult;

class EntityValidationResult extends ValidationResult {
    /** @var EntityPropertyValidationResult[] */
    private $propertyValidationResults;
    public function __construct($success = false, $error = null, $propertyValidationResults = []) {
        parent::__construct($success, $error);
        $this->propertyValidationResults = $propertyValidationResults;
    }
    public function jsonSerialize() {
        return array_merge_recursive(parent::jsonSerialize(), [ 'messages' => $this->propertyValidationResults ]);
    }
    /**
     * @return array|\Sm\Data\Entity\Property\Validation\EntityPropertyValidationResult[]
     */
    public function getPropertyValidationResults() {
        return $this->propertyValidationResults;
    }
}