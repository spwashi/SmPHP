<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 1:10 PM
 */

namespace Sm\Data\Property;


use Sm\Core\SmEntity\SmEntitySchema;

/**
 * Interface PropertySchema
 *
 * Something that describes a Property
 */
interface PropertySchema extends SmEntitySchema {
    public function getName();
    public function setName(string $name);
}