<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 4:11 PM
 */
use Sm\Communication\Request\Request;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\StringResolvable;

return [
    [ 'test' => '#Home::item', ],
    [ '{method}' => '#Home::test', ],
    [ 'Sm/ea/Hello' => StringResolvable::init("HELLO"), ],
    [ 'Hello' => StringResolvable::init("Hey there!"), ],
    [
        '$' =>
            FunctionResolvable::init(function (Request $Request = null) {
                return 'Hello!!';
            }),
    ],
];