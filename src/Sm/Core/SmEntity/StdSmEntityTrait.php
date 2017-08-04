<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:08 AM
 */

namespace Sm\Core\SmEntity;

use Sm\Core\Exception\UnimplementedError;

/**
 * Trait SmEntityCanBeConfiguredTrait
 *
 * For SmEntities that can be Configured
 *
 * @property $protoSmID
 */
trait StdSmEntityTrait {
    public function getSmID():?string {
        throw new UnimplementedError("Cannot yet get the SmID of this object");
    }
    public function getPrototypeSmID():?string {
        if (!isset($this->protoSmID)) throw new UnimplementedError("No prototypical smID set");
        return $this->protoSmID;
    }
}