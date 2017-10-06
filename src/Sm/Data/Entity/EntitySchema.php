<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 1:10 PM
 */

namespace Sm\Data\Entity;


use Sm\Core\SmEntity\SmEntitySchema;
use Sm\Data\Source\DiscretelySourced;

/**
 * Interface EntitySchema
 *
 * Something that describes a Entity
 */
interface EntitySchema extends SmEntitySchema, DiscretelySourced {
    public function getName();
    public function getProperties();
}