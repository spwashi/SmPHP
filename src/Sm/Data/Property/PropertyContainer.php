<?php


namespace Sm\Data\Property;

use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;

/**
 * Class PropertyContainer
 *
 * @method Property resolve($name = null): ?PropertySchema
 */
class PropertyContainer extends PropertySchemaContainer {
    public function __set($name, $value) {
        if (!($value instanceof Property)) {
            $this->set($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }
    public function set($name, $value = null) {
        if (is_array($name) && isset($value)) {
            throw new UnimplementedError("Not sure what to do with a name and value");
        }
        
        if ($name instanceof PropertyContainer) {
            $name = $name->getAll();
        }
        
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->set($key, $val);
            }
        } else {
            
            if ($value instanceof Property) {
                $value = $value->value;
            }
            /** @var  $property */
            $property = $this->resolve($name);
            if (!isset($property)) {
                throw new NonexistentPropertyException("Cannot set the value of a Property that doesn't exist");
            }
            $property->value = $value;
        }
        return $this;
    }
    
    protected function checkRegistrandIsCorrectType($registrand): void {
        if (!($registrand instanceof Property)) {
            throw new InvalidArgumentException("Can only add Properties to the PropertyContainer");
        }
    }
}