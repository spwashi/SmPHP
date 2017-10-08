<?php


use Sm\Communication\Request\Request;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\StringResolvable;

return [
    [ 'Sm/ea/Hello' => StringResolvable::init("HELLO"), ],
    [ 'Hello' => StringResolvable::init("Hey there!"), ],
    [ '$' => FunctionResolvable::init(function (Request $Request = null) { return 'Hello!!'; }), ],
];