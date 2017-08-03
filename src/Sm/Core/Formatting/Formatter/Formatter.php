<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 4:47 PM
 */

namespace Sm\Core\Formatting\Formatter;


/**
 * Interface Formatter
 *
 * Represents something that can "interpret" the items we pass into it to produce some sort of
 * formatted result that is of the type we specify
 *
 * @package Sm\Core\Formatting\Formatter
 */
interface Formatter {
    /**
     * Return the item Formatted in the specific way
     *
     * @param $item
     *
     * @return mixed
     */
    public function format($item);
}