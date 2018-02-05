<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 7:51 PM
 */

namespace Sm\Communication\Request;


use Sm\Core\Context\ContextDescriptor;
use Sm\Core\Exception\InvalidArgumentException;

/**
 * Class RequestDescriptor
 *
 * Class used to describe what a request should look like.
 *
 * @package Sm\Communication\Request
 */
class RequestDescriptor extends ContextDescriptor implements \JsonSerializable {
    public function compare($request) {
        parent::compare($request);
        if (!($request instanceof Request)) throw new InvalidArgumentException("Not working with a Request");
    }
    
    /**
     * Get an array of the variables that we are going to use to initialize Contexts from this Request.
     *
     * Implicitly coupled with the default Routing functionality, but I'm sure there are other uses.
     *
     * @see \Sm\Communication\Routing\Route
     *
     * @param \Sm\Communication\Request\Request $request The request to get the arguments from
     *
     * @return array
     */
    public function getArguments(Request $request) {
        return [];
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
        return [];
    }
}