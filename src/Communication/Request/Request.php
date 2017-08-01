<?php
/**
 * User: Sam Washington
 * Date: 1/29/17
 * Time: 10:53 PM
 */

namespace Sm\Communication\Request;

use Sm\Core\Context\Context;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;

/**
 * Class Request
 *
 * Class that is meant to be representative of whatever the client would request
 *
 * @package Sm\Communication\Request
 */
abstract class Request implements Context, \JsonSerializable {
    use HasObjectIdentityTrait;
    
    public static function init($item = null) {
        if ($item instanceof Request) return $item;
        return new static;
    }
}