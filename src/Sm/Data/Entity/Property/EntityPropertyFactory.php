<?php


namespace Sm\Data\Entity\Property;


use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyFactory;

class EntityPropertyFactory extends PropertyFactory {
    public function resolveDefault($parameters = null) {
        return !isset($parameters) ? new Property : null;
    }
    
}