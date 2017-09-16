<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 1:10 PM
 */

namespace Sm\Data\Model;


use Sm\Core\SmEntity\SmEntitySchema;
use Sm\Data\Source\DiscretelySourced;

/**
 * Interface ModelSchema
 *
 * Something that describes a Model
 */
interface ModelSchema extends SmEntitySchema, DiscretelySourced {
    public function getName();
    public function getProperties();
}