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
    /**
     * Get an array of the potential types that this Property will accept.
     *
     * Each datatype comes with its own set of validators and whatnot, which is
     * the main motivation for having the possibility of multiple datatypes represented.
     *
     * @return array
     */
    public function getDatatypes(): array;
    public function setDatatypes($datatypes);
}