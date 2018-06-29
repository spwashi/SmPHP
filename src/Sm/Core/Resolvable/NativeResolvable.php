<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:24 PM
 */

namespace Sm\Core\Resolvable;

/**
 * Class NativeResolvable
 *
 * This resolves exactly what we pass in.
 *
 * @package Sm\Core\Resolvable
 */
class NativeResolvable extends AbstractResolvable implements \JsonSerializable {
    public function resolve() {
        return $this->subject;
    }
    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize() {
        return $this->subject;
    }
}