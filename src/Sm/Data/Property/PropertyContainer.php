<?php


namespace Sm\Data\Property;

use Sm\Core\Exception\InvalidArgumentException;

/**
 * Class PropertyContainer
 *
 * @method Property resolve($name = null): ?PropertySchema
 */
class PropertyContainer extends PropertySchemaContainer {
    public function __set($name, $value) {
        if (!($value instanceof Property)) {
            $property        = $this->resolve($name);
            $property->value = $value;
        } else {
            parent::__set($name, $value);
        }
    }
    protected function checkRegistrandIsCorrectType($registrand): void {
        if (!($registrand instanceof Property)) {
            throw new InvalidArgumentException("Can only add Properties to the PropertyContainer");
        }
    }
}