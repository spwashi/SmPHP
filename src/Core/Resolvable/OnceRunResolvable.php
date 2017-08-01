<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:34 AM
 */

namespace Sm\Core\Resolvable;


/**
 * Class OnceCalledResolvable
 *
 * Resolvable that runs a function, but only once.
 *
 * @package Sm\Core\Resolvable
 */
class OnceRunResolvable extends AbstractResolvable {
    /**
     * Has the Function already been run?
     *
     * @var bool
     */
    public $has_been_called = false;
    /** @var mixed $last_value The value of the OnceRunResolvable if it hasn't already been run */
    public $last_value = null;
    /** @var  \Sm\Core\Resolvable\Resolvable $subject The thing that we are going to resolve once */
    protected $subject;
    /**
     * @param \Sm\Core\Resolvable\Resolvable|mixed $subject
     *
     * @return $this
     */
    public function setSubject($subject) {
        $subject = $this->getFactoryContainer()
                        ->resolve(ResolvableFactory::class)
                        ->build($subject);
        parent::setSubject($subject);
        return $this;
    }
    /**
     * Method for what happens when the Singleton Function is cloned
     */
    public function __clone() {
        $this->reset();
    }
    public function reset() {
        $this->has_been_called = false;
        $this->last_value      = null;
        return $this;
    }
    public function resolve($_ = null) {
        $arguments = func_get_args();
        
        # If we've already called this function, we don't need to bother trying to call it again
        if ($this->has_been_called) return $this->last_value;
    
        $new_result            = $this->subject->resolve(...$arguments);
        $this->has_been_called = true;
        return ($this->last_value = $new_result);
    }
}