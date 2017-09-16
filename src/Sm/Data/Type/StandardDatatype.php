<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 8:46 PM
 */

namespace Sm\Data\Type;


use Sm\Core\Resolvable\AbstractResolvable;
use Sm\Core\Resolvable\ResolvableFactory;
use Sm\Core\SmEntity\StdSmEntityTrait;

abstract class StandardDatatype extends AbstractResolvable implements Datatype {
    use StdSmEntityTrait;
    /** @var  \Sm\Core\Resolvable\Resolvable $subject */
    protected $subject;
    
    public function setSubject($subject) {
        $this->subject = static::resolveType($subject);
        return $this;
    }
    
    /**
     * @param null|mixed $_ ,..
     *
     * @return mixed
     */
    public function resolve($_ = null) {
        return $this->subject->resolve();
    }
    function jsonSerialize() {
        $subject = "$this->subject";
        if (strlen($subject)) {
            return $subject;
        }
        $class = static::class;
        $expl  = explode('\\', $class);
        end($expl);
        return "[" . $expl[ key($expl) ] . "]";
    }
    /**
     * Represent the subject internally in a particular way
     *
     * @param $subject
     *
     * @return \Sm\Core\Resolvable\Resolvable
     */
    public static function resolveType($subject) {
        return ResolvableFactory::init($subject)->resolve($subject);
    }
}