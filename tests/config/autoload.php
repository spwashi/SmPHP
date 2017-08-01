<?php
/**
 * User: Sam Washington
 * Date: 4/26/17
 * Time: 6:02 PM
 */
spl_autoload_register(function ($class_string) {
    $class   = explode('\\', $class_string);
    $is_test = end($class) && strpos($class[ key($class) ], 'Test') > 0;
    $path    = $is_test ? TEST_PATH : SRC_PATH;
    if ($class[0] === 'Sm') $class[0] = 'SmPHP';
    $class = implode('/', $class);
    $path  .= "{$class}.php";
    if (is_file($path)) require_once($path);
});
