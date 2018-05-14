<?php


namespace Sm\Data\Entity\Property;


use Sm\Core\Context\Context;
use Sm\Data\Entity\Property\Validation\EntityPropertyValidationResult;
use Sm\Data\Evaluation\Validation\ValidationResult;
use Sm\Data\Property\Property;
use Sm\Data\Type\Exception\CannotCastException;

class EntityProperty extends Property implements EntityPropertySchema {
    public function validate(Context $context = null): ?ValidationResult {
        try {
            $resolved = $this->resolve();
        } catch (CannotCastException $e) {
            return new EntityPropertyValidationResult(false, $e->getMessage());
        }
        return null;
    }
}