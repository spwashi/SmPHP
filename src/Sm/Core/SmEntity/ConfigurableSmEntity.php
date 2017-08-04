<?php
/**
 * User: Sam Washington
 * Date: 8/2/17
 * Time: 11:02 PM
 */

namespace Sm\Core\SmEntity;

/**
 * Interface ConfigurableSmEntity
 *
 * Represents SmEntities that can be configured using some common syntax
 *
 * @package Sm\Core\SmEntity
 */
interface ConfigurableSmEntity extends SmEntity {
    /**
     * Configure the SmEntitiy
     *
     * @param mixed|\Sm\Core\SmEntity\SmEntitySchematic $configuration
     *
     * @return mixed
     */
    public function configure($configuration);
}