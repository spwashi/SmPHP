<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:24 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Core\Resolvable\Error\UnresolvableException;

/**
 * Class FunctionResolvable
 *
 * A resolvable
 *
 * @package Sm\Core\Resolvable
 */
class FunctionResolvable extends AbstractResolvable {
    /** @var array An array of Arguments to add to the FunctionResolvable */
    protected $arguments = [];
    public function __toString() {
        return "[function]";
    }
    
    /**
     * @param null $_ The arguments. When we resolve the function,
     *                the arguments passed in to this function are
     *                appended to the arguments set via the setArguments function
     *
     * @return mixed
     * @throws \Sm\Core\Resolvable\Error\UnresolvableException
     */
    public function resolve($_ = null) {
        $arguments = array_merge($this->arguments, func_get_args());
        $subject   = $this->subject;
        
        if (is_string($subject) && strpos($subject, '::')) {
            $subject = explode('::', $subject);
        }
        
        if (!is_callable($subject)) {
            throw  new UnresolvableException("Must be a callable function");
        }
        
        return call_user_func_array($this->subject, $arguments);
    }
    /**
     * Get the array of arguments that are going to be passed in to the function
     *
     * @return array
     */
    public function getArguments(): array {
        return $this->arguments;
    }
    /**
     * If we want to set the arguments of the FunctionResolvable in advance, we can do it here.
     * This is similar to binding parameters to the function.
     *
     * @param array ...$arguments
     *
     * @return $this
     */
    public function setArguments(...$arguments) {
        $this->arguments = $arguments;
        return $this;
    }
    function __debugInfo() {
        return array_merge(parent::__debugInfo(), [
            'arguments' => $this->arguments,
            'subject'   => is_object($this->subject) ? (method_exists($this->subject, '__debugInfo') ? $this->subject->__debugInfo() : get_class($this->subject)) : gettype($this->subject),
        ]);
    }
}