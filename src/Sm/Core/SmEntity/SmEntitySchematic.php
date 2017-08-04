<?php
/**
 * User: Sam Washington
 * Date: 8/2/17
 * Time: 11:07 PM
 */

namespace Sm\Core\SmEntity;


/**
 * Interface FrameworkEntityConfiguration
 *
 * Represents an object that can be used to initalize a SmEntity
 *
 * @package Sm\Core\Framework\Configuration
 */
interface SmEntitySchematic extends SmEntitySchema {
    public function load($configuration);
}