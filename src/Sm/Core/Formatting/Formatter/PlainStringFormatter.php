<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 3:49 PM
 */

namespace Sm\Core\Formatting\Formatter;


use Sm\Core\Resolvable\StringResolvable;

/**
 * Class PlainStringFormatter
 *
 * Formats things to be plain strings
 *
 * @package Sm\Core\Formatting\Formatter
 */
class PlainStringFormatter implements Formatter {
    /**
     * @param $item
     *
     * @return string
     */
    public function format($item) {
        return StringResolvable::init()->setSubject($item)->resolve();
    }
}