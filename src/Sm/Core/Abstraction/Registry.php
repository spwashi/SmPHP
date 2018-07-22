<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:04 PM
 */

namespace Sm\Core\Abstraction;


interface Registry {
    public function register($name, $registrant = null);
    public function resolve();
}