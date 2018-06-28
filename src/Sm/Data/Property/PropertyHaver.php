<?php
/**
 * User: Sam Washington
 * Date: 3/16/17
 * Time: 12:32 AM
 */

namespace Sm\Data\Property;

/**
 * Interface PropertyHaver
 *
 * Represents something that holds a specific set of properties
 *
 * @package Sm\Data\Property
 * @method getProperties():PropertyContainer
 */
interface PropertyHaver extends PropertyHaverSchema {
    public function instantiateProperty(PropertySchema $propertySchema): Property;
}