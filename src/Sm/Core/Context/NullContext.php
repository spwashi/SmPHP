<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/27/18
 * Time: 1:40 PM
 */

namespace Sm\Core\Context;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;

/**
 * Class NullContext
 *
 * Represents a context that is *nothing*
 *
 * @package Sm\Core\Context
 */
class NullContext implements Context {
	use HasObjectIdentityTrait;
}