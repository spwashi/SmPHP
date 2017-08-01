<?php
/**
 * User: Sam Washington
 * Date: 2/5/17
 * Time: 11:47 PM
 */
spl_autoload_register(function ($c_name) {
    $c_name = explode('\\', $c_name);
    array_shift($c_name);
    $c_name = implode('/', $c_name);
    $path   = EXAMPLE_APP_PATH . "{$c_name}.php";
    if (is_file($path)) require_once($path);
});