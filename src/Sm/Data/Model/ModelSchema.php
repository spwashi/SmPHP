<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 1:10 PM
 */

namespace Sm\Data\Model;


use Sm\Core\SmEntity\SmEntitySchema;
use Sm\Data\Property\PropertyHaverSchema;
use Sm\Data\Property\PropertySchemaContainer;
use Sm\Data\Source\DiscretelySourced;

/**
 * Interface ModelSchema
 *
 * Something that describes a Model
 */
interface ModelSchema extends SmEntitySchema, PropertyHaverSchema, DiscretelySourced {
    public function getName();
    /**
     * @param array $property_names
     *
     * @return PropertySchemaContainer
     */
    public function getProperties($property_names = []);
    public function set($name, $value = null);
}