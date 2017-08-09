<?php
/**
 * User: Sam Washington
 * Date: 8/2/17
 * Time: 11:07 PM
 */

namespace Sm\Core\SmEntity;

use Sm\Core\Schema\Schematic;


/**
 * Interface FrameworkEntityConfiguration
 *
 * Represents an object that can be used to initalize a SmEntity
 *
 */
interface SmEntitySchematic extends SmEntitySchema, Schematic {
    public function load($configuration);
    /**
     * Get the SmID of the Prototype of these SmEntities
     *
     * @return null|string
     */
    public function getPrototypeSmID():?string;
}