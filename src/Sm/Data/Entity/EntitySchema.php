<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 1:10 PM
 */

namespace Sm\Data\Entity;


use Sm\Core\SmEntity\SmEntitySchema;

/**
 * Interface EntitySchema
 *
 * Something that describes a Entity
 */
interface EntitySchema extends SmEntitySchema {
    public function getName();
    public function setName(string $name);
}