<?php


namespace Sm\Data\Model;


use Sm\Data\Property\PropertyContainerInstance;
use Sm\Data\Property\PropertyHaver;

/**
 * Interface ModelInstance
 * @package Sm\Data\Model
 * @property-read $properties
 */
interface ModelInstance extends ModelSchema, PropertyHaver {
    /** @return  PropertyContainerInstance */
    public function getProperties($property_names = []);
}