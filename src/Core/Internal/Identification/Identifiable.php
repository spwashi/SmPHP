<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 6:12 PM
 */

namespace Sm\Core\Internal\Identification;


/**
 * interface Identifiable
 *
 * Represents an object that we can Identify
 *
 * @package Sm\Core\Internal\Identification
 */
interface Identifiable {
    public function getObjectId();
}