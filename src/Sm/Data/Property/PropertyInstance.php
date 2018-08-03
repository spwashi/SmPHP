<?php


namespace Sm\Data\Property;


use Sm\Core\Resolvable\Resolvable;
interface PropertyInstance extends PropertySchema, Resolvable {
    public function getValue();
    public function setValue($value);
}