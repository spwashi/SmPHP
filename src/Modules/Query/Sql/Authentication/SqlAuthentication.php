<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 5:25 PM
 */

namespace Sm\Modules\Query\Sql\Authentication;

/**
 * Class SqlAuthentication
 *
 * Represents an Authentication that will be used to authorize connections via Sql
 *
 * @package Sm\Modules\Query\Sql\Authentication
 */
interface SqlAuthentication {
    /** @return */
    public function getConnection();
}