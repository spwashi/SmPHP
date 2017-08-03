<?php
/**
 * User: Sam Washington
 * Date: 7/24/17
 * Time: 8:02 PM
 */

namespace Sm\Query\Proxy;


/**
 * Class String_QueryProxy
 *
 * Meant to represent just strings
 *
 * @package Sm\Query\Proxy
 */
class String_QueryProxy extends QueryProxy {
    protected $subject;
    public function __construct(string $subject = null) {
        parent::__construct();
        $this->subject = $subject;
    }
    public static function init($string = null) {
        return new static(...func_get_args());
    }
    public function getQuery() {
        return $this->subject;
    }
}