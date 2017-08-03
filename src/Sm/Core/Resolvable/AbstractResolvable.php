<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:56 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Core\Factory\HasFactoryContainerTrait;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Util;

/**
 * Class Resolvable
 *
 * Represents something that will eventually have an end value (vague, I know)
 * Basically, this class is meant to provide a consistent interface for interacting
 * with the various objects/types that we might come across in this framework.
 *
 * @package Sm\Core\Resolvable
 */
abstract class AbstractResolvable implements Resolvable {
    use HasFactoryContainerTrait;
    use HasObjectIdentityTrait;
    /** @var  mixed $subject The thing that this Resolvable is wrapping */
    protected $subject;
    
    ##########################################################################
    /**
     * Resolvable constructor.
     *
     * @param mixed $subject
     */
    public function __construct($subject = null) {
        $this->setSubject($subject);
        $this->createSelfID();
    }
    /**
     * Static constructor for resolvables
     *
     * @param mixed $item
     *
     * @return static
     */
    public static function init($item = null) {
        if (is_a($item, static::class)) {
            return $item;
        }
        return new static($item);
    }
    /**
     * Convert this to a string
     *
     * @return string
     */
    public function __toString() {
        if ($this->subject !== $this && Util::canBeString($this->subject)) {
            return "$this->subject";
        } else {
            $resolved = $this->resolve();
            if ($resolved !== $this && Util::canBeString($resolved)) {
                return "$resolved";
            } else {
                return '';
            }
        }
    }
    /**
     * Resolve this Resolvable with arguments
     *
     * @return mixed
     */
    public function __invoke() {
        return $this->resolve(...func_get_args());
    }
    public function __debugInfo() {
        return [
            'id'      => $this->getObjectId(),
            'subject' => $this->subject,
        ];
    }
    
    #########################################################################
    
    /**
     * Get the subject of the Resolvable (The thing that this Resolvable is wrapping)
     *
     * @return mixed
     */
    public function getSubject() {
        return $this->subject;
    }
    /**
     * Set the subject that the Resolvable is going to use as a reference
     *
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }
    
    #########################################################################
    
    abstract public function resolve();
}