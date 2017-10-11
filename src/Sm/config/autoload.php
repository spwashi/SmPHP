<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 1:11 AM
 */
spl_autoload_register(function ($class_string) {
    $class = explode('\\', $class_string);
    $class = implode('/', $class);
    $path  = SM_SRC_PATH . "{$class}.php";
    if (is_file($path)) require_once($path);
});