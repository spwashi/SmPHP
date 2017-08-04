<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:13 AM
 */

namespace Sm\Core\SmEntity;


use Sm\Core\Factory\StandardFactory;

/**
 * Class SmEntityFactory
 *
 * Factory to create or resolve references to SmEntities
 *
 * @package Sm\Core\SmEntity
 */
abstract class SmEntityFactory extends StandardFactory {
    protected function canCreateClass($object_type) {
        return is_a($object_type, SmEntity::class, true);
    }
}