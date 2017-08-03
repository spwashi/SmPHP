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
abstract class RequestDescriptor extends ContextDescriptor {
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
    abstract public function getArguments(Request $request);
}