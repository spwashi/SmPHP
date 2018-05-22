<?php
/**
 * User: spwashi2
 * Date: 2/2/2017
 * Time: 5:23 PM
 */

namespace Sm\Core\Resolvable;

use Sm\Core\Util;
use Sm\Data\Type\Exception\CannotCastException;

/**
 * Class DateResolvable
 *
 * Class that represents a date
 *
 * @package Sm\Core\Resolvable
 */
class DateTimeResolvable extends AbstractResolvable {
    const NOW = 'now';
    /** @var \DateTime $subject */
    protected $subject;
    /**
     *
     * @param $subject
     *
     * @return $this
     * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
     */
    public function setSubject($subject) {
        if ($subject === false || !isset($subject)) {
            $subject = null;
        } else if (is_string($subject) && strtolower($subject) === 'now') {
            $subject = \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));
        } else if (!($subject instanceof \DateTime)) {
            $self = null;
            throw new CannotCastException("Cannot yet resolve dates from other types -- " . Util::getShape($subject) . (is_scalar($subject) ? ' -- ' . $subject : null) . ' -- given');
        }
        /** @var static $self */
        $self = parent::setSubject($subject);
        return $self;
    }
    
    public function resolve($arguments = null) {
        return $this->subject;
    }
    public function jsonSerialize() {
        return $this->subject->format('Y-m-d H:i:s.u');
    }
}