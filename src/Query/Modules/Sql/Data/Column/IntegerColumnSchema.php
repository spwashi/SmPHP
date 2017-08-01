<?php
/**
 * User: Sam Washington
 * Date: 7/18/17
 * Time: 12:24 PM
 */

namespace Sm\Query\Modules\Sql\Data\Column;

/**
 * Class IntegerColumnSchema
 *
 * @package Sm\Query\Modules\Sql\Data\Column
 */
class IntegerColumnSchema extends ColumnSchema {
    protected $type           = 'INT';
    protected $auto_increment = false;
    public function isAutoIncrement() {
        return $this->auto_increment;
    }
    public function setAutoIncrement($do_it = true) {
        $this->auto_increment = $do_it;
        return $this;
    }
    public function setLength(int $length) {
        return parent::setLength($length);
    }
}