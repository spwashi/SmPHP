<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 5:25 PM
 */

namespace Sm\Query\Modules\Sql\Authentication;

/**
 * Class SqlAuthentication
 *
 * Represents an Authentication that will be used to authorize connections via Sql
 *
 * @package Sm\Query\Modules\Sql\Authentication
 */
interface SqlAuthentication {
    /** @return */
    public function getConnection();
}