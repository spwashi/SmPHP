<?php
/**
 * User: Sam Washington
 * Date: 7/18/17
 * Time: 12:24 PM
 */

namespace Sm\Modules\Sql\Data\Column;

/**
 * Class DateTimeColumnSchema
 *
 * @package Sm\Modules\Sql\Data\Column
 */
class DateTimeColumnSchema extends ColumnSchema {
    const CURRENT_TIMESTAMP = 'CURRENT_TIMESTAMP';
    const NOW               = 'NOW';
    
    protected $type           = 'DATETIME';
    protected $auto_increment = false;
    protected $default;
    protected $on_update;
    
    /**
     * Set the default value of this row
     *
     * @param $default
     *
     * @return $this
     */
    public function setDefault($default) {
        $this->default = $default;
        return $this;
    }
    
    /**
     * Set what the value of this DateTimeColumn should be
     *
     * @param mixed $on_update
     *
     * @return DateTimeColumnSchema
     */
    public function setOnUpdate($on_update = DateTimeColumnSchema::CURRENT_TIMESTAMP) {
        $this->on_update = $on_update;
        return $this;
    }
    
    public function getOnUpdate() {
        return $this->on_update;
    }
    public function getDefault() {
        return $this->default;
    }
}