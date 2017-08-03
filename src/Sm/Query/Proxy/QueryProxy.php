<?php
/**
 * User: Sam Washington
 * Date: 7/24/17
 * Time: 7:55 PM
 */

namespace Sm\Query\Proxy;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;
use Sm\Core\Proxy\Proxy;

/**
 * Class QueryProxy
 *
 * @package Sm\Query
 */
class QueryProxy implements Proxy, Identifiable {
    use HasObjectIdentityTrait;
    public function __construct() {
        $this->createSelfID();
    }
}