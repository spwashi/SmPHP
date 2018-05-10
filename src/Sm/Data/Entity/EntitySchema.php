<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 1:10 PM
 */

namespace Sm\Data\Entity;


use Sm\Core\Context\Context;
use Sm\Core\SmEntity\SmEntitySchema;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\PropertyHaverSchema;

/**
 * Interface EntitySchema
 *
 * Something that describes a Entity
 */
interface EntitySchema extends SmEntitySchema, PropertyHaverSchema {
    public function getName();
    public function setName(string $name);
    public function getPersistedIdentity(): ?ModelSchema;
    public function proxyInContext(Context $context):? ContextualizedEntityProxy;
}