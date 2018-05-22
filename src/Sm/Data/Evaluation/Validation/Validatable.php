<?php


namespace Sm\Data\Evaluation\Validation;


use Sm\Core\Context\Context;

interface Validatable {
    public function validate(Context $context = null): ?ValidationResult;
}