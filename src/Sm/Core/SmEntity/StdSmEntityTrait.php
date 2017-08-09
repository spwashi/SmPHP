<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:08 AM
 */

namespace Sm\Core\SmEntity;

/**
 * Trait SmEntityCanBeConfiguredTrait
 *
 * For SmEntities that can be Configured
 *
 */
trait StdSmEntityTrait {
    protected $_smID;
    protected $_name;
    public function getSmID():?string {
        return $this->_smID;
    }
    public function getName():?string {
        return $this->_name;
    }
    public function setName(string $name) {
        $this->_name = $name;
        return $this;
    }
}