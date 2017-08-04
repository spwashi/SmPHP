<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 1:01 PM
 */

namespace Sm\Core\SmEntity;


use Sm\Core\Schema\Schema;

/**
 * Interface SmEntitySchema
 *
 * Represents things that describe SmEntities.
 *
 * @package Sm\Core\SmEntity
 */
interface SmEntitySchema extends Schema {
    /**
     * Get an Identifier that will remain consistent for this particular
     * chunk of data across each SmFramework implementation
     *
     * @return null|string
     */
    public function getSmID():?string;
}