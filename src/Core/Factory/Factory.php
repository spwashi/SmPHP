<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 1:56 PM
 */

namespace Sm\Core\Factory;

use Sm\Core\Abstraction\Registry;


/**
 * Class Factory
 *
 * Like a Container, but designed to return the same type of class consistently
 *
 * @package Sm\Core\Factory
 */
interface Factory extends Registry {
    /**
     * Return whatever this factory refers to based on some sort of operand
     *
     * @return null
     */
    public function build();
}