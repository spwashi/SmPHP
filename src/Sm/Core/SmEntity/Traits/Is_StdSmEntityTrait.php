<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:08 AM
 */

namespace Sm\Core\SmEntity\Traits;

use Sm\Core\Internal\Identification\HasObjectIdentityTrait;

/**
 * Trait SmEntityCanBeConfiguredTrait
 *
 * For SmEntities that can be Configured
 *
 */
trait Is_StdSmEntityTrait {
    protected $_smID;
    protected $_name;
    use HasObjectIdentityTrait;
    
    protected function setSmID(string $smID = null) {
        $this->_smID = $smID ?? $this->_smID;
    }
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