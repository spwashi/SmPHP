<?php


namespace Sm\Data\Entity\Property;


use Sm\Core\Context\Context;
use Sm\Data\Entity\Entity;
use Sm\Data\Entity\EntitySchema;
use Sm\Data\Entity\Property\Validation\EntityPropertyValidationResult;
use Sm\Data\Entity\Validation\EntityValidationResult;
use Sm\Data\Evaluation\Validation\ValidationResult;
use Sm\Data\Property\Property;
use Sm\Data\Property\Validation\PropertyValidationResult;
use Sm\Data\Type\String_;

/**
 * @property-read EntityPropertySchematic $schematic
 */
class EntityProperty extends Property implements EntityPropertySchema {
    /**
     * @var Entity $owner
     * @todo probably want to replace this with an EntityInstance
     */
    protected $owner;

    public function __get($name) {
        switch ($name) {
            case 'schematic':
                return $this->getEffectiveSchematic();
            default:
                return parent::__get($name);
        }
    }
    public function setOwner(Entity $entitySchema) {
        $this->owner = $entitySchema;
        return $this;
    }
    public function setValue($value) {
        $res = parent::setValue($value);
        if ($this->owner) $this->owner->markPropertyUpdated($this);
        return $res;
    }

    public function validate(Context $context = null): ?ValidationResult {
        $parent = parent::validate($context);

        if (!isset($parent)) {
            $resolved_value = $this->resolve();
            $datatype       = $this->getPrimaryDatatype();
            /** @var \Sm\Data\Entity\Property\EntityPropertySchematic $schematic */
            $schematic = $this->getEffectiveSchematic();


            if ($schematic instanceof EntityPropertySchematic && $datatype instanceof String_) {
                $minLength = $schematic ? $schematic->getMinLength() : null;
                if (isset($minLength) && strlen($resolved_value) < $minLength) {
                    $too_short_message        = 'Too short - must be at least ' . $minLength . ' characters';
                    $propertyValidationResult = new EntityPropertyValidationResult(false, $too_short_message);
                    $propertyValidationResult->setFailedAttributes([PropertyValidationResult::ATTR__LENGTH => $resolved_value]);
                    return $propertyValidationResult;
                }

            }
        }
        return $parent;
    }
    public function jsonSerialize() {
        $value = $this->value;
        if ($value instanceof \DateTime) {
            return $value->format(DATE_ISO8601);
        } else {
            return parent::jsonSerialize();
        }
    }

    protected function getValidationResult($success = false, $error = null): PropertyValidationResult {
        return new EntityPropertyValidationResult(...func_get_args());
    }
}