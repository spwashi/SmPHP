<?php
/**
 * User: Sam Washington
 * Date: 7/13/17
 * Time: 9:45 AM
 */

namespace Sm\Modules\Query\Sql\Formatting;


use Sm\Core\Formatting\Formatter\PlainStringFormatter;

class StdSqlFormatter extends PlainStringFormatter {
    
    /**
     * @param $item
     *
     * @return string
     */
    public function format($item) {
        if (!isset($item)) return 'NULL';
        if (is_numeric($item)) return $item;
        $result = parent::format($item);
        return '"' . $result . '"';
    }
}