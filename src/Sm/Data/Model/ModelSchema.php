<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 1:10 PM
 */

namespace Sm\Data\Model;


use Sm\Core\SmEntity\SmEntitySchematic;

/**
 * Interface ModelSchema
 *
 * Something that describes a Model
 */
interface ModelSchema extends SmEntitySchematic {
    public function getName();
}