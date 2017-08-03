<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 9:36 PM
 */

namespace Sm\Core\Schema;


/**
 * Interface ComparableSchema
 *
 * Schemas that can be compared
 *
 * @package Sm\Core\Schema
 */
interface ComparableSchema extends Schema {
    /**
     * Check to see if a Schema matches an item
     *
     * @param $item
     *
     * @return
     */
    public function compare($item);
}