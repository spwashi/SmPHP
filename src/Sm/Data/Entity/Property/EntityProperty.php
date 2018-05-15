<?php


namespace Sm\Data\Entity\Property;


use Sm\Core\Context\Context;
use Sm\Data\Entity\Property\Validation\EntityPropertyValidationResult;
use Sm\Data\Evaluation\Validation\ValidationResult;
use Sm\Data\Property\Property;
use Sm\Data\Type\Exception\CannotCastException;
use Sm\Data\Type\String_;

class EntityProperty extends Property implements EntityPropertySchema {
    public function validate(Context $context = null): ?ValidationResult {
        try {
            $resolved_value = $this->resolve();
            $datatype       = $this->getPrimaryDatatype();
            /** @var \Sm\Data\Entity\Property\EntityPropertySchematic $schematic */
            $schematic = $this->getEffectiveSchematic();
            $maxLength = $schematic ? $schematic->getLength() : null;
            $minLength = $schematic ? $schematic->getMinLength() : null;
            
            if (isset($maxLength) && $datatype instanceof String_ && strlen($resolved_value) > $maxLength) {
                return new EntityPropertyValidationResult(false, 'Too long - can only be ' . $maxLength . ' characters');
            }
            
            if (isset($minLength) && $datatype instanceof String_ && strlen($resolved_value) < $minLength) {
                return new EntityPropertyValidationResult(false, 'Too short - must be at least ' . $minLength . ' characters');
            }
            
        } catch (CannotCastException|\Exception $e) {
            return new EntityPropertyValidationResult(false, $e->getMessage());
        }
        return null;
    }
}