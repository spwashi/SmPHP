<?php
/**
 * User: Sam Washington
 * Date: 8/2/17
 * Time: 10:12 PM
 */

namespace Sm\Core\SmEntity;


/**
 * Interface SmEntity
 *
 * Class that represents a Framework Entity.
 * These are the objects/data structures that we will likely see in each implementation of this Framework.
 *
 * @package Sm\Core\SmEntity
 */
interface SmEntity {
    public function getSmId():?string;
}