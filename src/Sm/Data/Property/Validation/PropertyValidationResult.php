<?php


namespace Sm\Data\Property\Validation;


use Sm\Data\Evaluation\Validation\ValidationResult;

class PropertyValidationResult extends ValidationResult {
	const ATTR__LENGTH = 'LENGTH';
	const ATTR__VALUE  = 'VALUE';

	const ERROR__TOO_SHORT = 'TOO_SHORT';
	const ERROR__TOO_LONG  = 'TOO_LONG';
}