<?php
/**
 * User: Sam Washington
 * Date: 6/26/17
 * Time: 10:13 PM
 */

namespace Sm\Core\Context;

use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\TypeMismatchException;
use Sm\Core\Schema\ComparableSchema;
use Sm\Core\Schema\Schema;
use Sm\Core\Util;


/**
 * Class ContextDescriptor
 *
 * This class is meant to serve as an interface describing the features of a Context that we are looking for
 *
 * @package Sm\Core\Context
 */
class ContextDescriptor implements ComparableSchema {
    /** @var array $matching_context_classes An array of the classes that will match this Context */
    protected $matching_context_classes = null;
    
    /**
     * @param \Sm\Core\Context\Context $request The Item that we are comparing it to
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\TypeMismatchException
     */
    public function compare($request) {
        if (!($request instanceof Context)) throw new InvalidArgumentException("Cannot compare items that are not Contexts");
        
        if ($context_classes = $this->matching_context_classes) {
            $this->check_context_classes_equal($request);
        }
    }
    /**
     * Set an array of the classes that match this Context Descriptor
     *
     * @param array $matching_context_classes
     *
     * @return $this
     */
    public function setMatchingContextClasses(array $matching_context_classes) {
        $this->matching_context_classes = $matching_context_classes;
        return $this;
    }
    /**
     * Compare to see if a Context's classes are the same as the ones this ContextDescriptor knows about
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @throws \Sm\Core\Exception\TypeMismatchException
     */
    protected function check_context_classes_equal(Context $context) {
        $isOneOfListedTypes = Util::isOneOfListedTypes($context,
                                                       $this->matching_context_classes);
        
        if (!$isOneOfListedTypes) {
            throw new TypeMismatchException("These Contexts do not match");
        }
    }
    protected function specify($requirement_for_completion, $matching_context_classes) { }
}