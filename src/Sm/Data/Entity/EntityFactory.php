<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:23 AM
 */

namespace Sm\Data\Entity;


use Sm\Core\SmEntity\SmEntityFactory;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * Class EntityFactory
 * @method Entity resolve($name = null)
 */
class EntityFactory extends SmEntityFactory {
    /**
     * @param object|string $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        return parent::canCreateClass($object_type) && is_a($object_type, Entity::class);
    }
}