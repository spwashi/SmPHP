<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 1:11 AM
 */
spl_autoload_register(function ($class_string) {
    $class = explode('\\', $class_string);
    if (isset($class[0]) && isset($class[1]) && $class[1] === 'Test') {
        $class[0] = array_shift($class) . $class[0];
    }
    if ($class[0] === 'Sm') $class[0] = 'SmPHP';
    $class = implode('/', $class);
    
    $path = SRC_PATH . "{$class}.php";
    if (is_file($path)) {
        require_once($path);
    }
});