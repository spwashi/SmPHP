<?php

spl_autoload_register(function ($c_name) {
    $c_name = explode('\\', $c_name);
    array_shift($c_name);
    $c_name = implode('/', $c_name);
    # TODO
    $path = "{$c_name}.php";
    if (is_file($path)) require_once($path);
});