<?php


namespace Sm\Data\Property;


use Sm\Core\Schema\SchematicInstantiator;

interface PropertyInstantiator extends SchematicInstantiator {
    public function instantiate($schematic = null): Property;
}