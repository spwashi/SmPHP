<?php
/**
 * User: spwashi2
 * Date: 2/2/2017
 * Time: 5:23 PM
 */

namespace Sm\Core\Resolvable;

use Sm\Core\Resolvable\Error\UnresolvableException;

/**
 * Class DateResolvable
 *
 * Class that represents a date
 *
 * @package Sm\Core\Resolvable
 */
class DateResolvable extends AbstractResolvable {
    /**
     *
     * @param $subject
     *
     * @return $this
     * @throws \Sm\Core\Resolvable\Error\UnresolvableException
     */
    public function setSubject($subject) {
        if (!isset($subject)) {
            $subject = \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));
        } else {
            throw new UnresolvableException("Cannot yet resolve dates from other types");
        }
        /** @var static $self */
        $self = parent::setSubject($subject);
        return $self;
    }
    
    public function resolve($arguments = null) {
        return $this->subject;
    }
}