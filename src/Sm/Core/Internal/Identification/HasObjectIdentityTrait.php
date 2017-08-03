<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 6:33 PM
 */

namespace Sm\Core\Internal\Identification;

/**
 * Class HasObjectIdentityTrait
 *
 *
 * Used for objects that implement the Identifiable interface.
 *
 * @see        \Sm\Core\Internal\Identification\Identifiable
 * @package    \Sm\Core\Internal\Identification
 * @mixin      Identifiable
 */
trait HasObjectIdentityTrait {
    protected $_object_id;
    /**
     * Get the ID that uniquely identifies this object.
     *
     * @return string
     */
    public function getObjectId():?string {
        return $this->_object_id;
    }
    /**
     * Set the object ID. Only permit this to happen once.
     *
     * @param $object_id
     *
     * @return $this
     */
    public function setObjectId($object_id) {
        $this->_object_id = $this->_object_id ?? $object_id;
        return $this;
    }
    /**
     * Generate an ID and set it on this class
     */
    protected function createSelfID() {
        $this->setObjectId(Identifier::generateIdentity($this));
        return $this->_object_id;
    }
}
