<?php
/**
 * User: Sam Washington
 * Date: 4/13/17
 * Time: 10:24 AM
 */

namespace Sm\Core\Container\Cache;


interface CacheInterface {
    function start($key = '-');
    function cache($index, $registrand);
    function end($key = '-');
}