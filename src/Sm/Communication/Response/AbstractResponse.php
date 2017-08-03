<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 12:55 AM
 */

namespace Sm\Communication\Response;


use Sm\Core\Resolvable\DateResolvable;

/**
 * Class Response
 *
 * Represents Responses
 *
 * @package Sm\Communication\Response
 */
abstract class AbstractResponse implements Response {
    public function __construct() {
        # Update the creation/access dates
        $this->creation_dt = DateResolvable::init();
        $this->access_dt   = null;
    }
    public static function init() {
        return new static(...func_get_args());
    }
}