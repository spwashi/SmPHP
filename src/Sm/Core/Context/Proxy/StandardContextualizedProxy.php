<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 11:01 AM
 */

namespace Sm\Core\Context\Proxy;


use Sm\Core\Context\Context;
use Sm\Core\Context\StandardContext;
use Sm\Core\Proxy\Proxy;

/**
 * Class StandardContextualizedProxy
 *
 * Typical ContextualizedProxy
 *
 * @package Sm\Core\Context\Proxy
 */
abstract class StandardContextualizedProxy extends StandardContext implements Proxy, ContextualizedProxy {
    /** @var  mixed $subject */
    protected $subject;
    /** @var  \Sm\Core\Context\Context $context */
    protected $context;
    /**
     * StandardContextualizedProxy constructor.
     *
     * @param         $subject
     * @param Context $context
     */
    public function __construct($subject, $context = null) {
        parent::__construct();
        $this->subject = $subject;
        if ($context) $this->setContext($context);
    }
    public function getContext(): Context {
        return $this->context;
    }
    protected function setContext(Context $context) {
        $this->context = $context;
        return $this;
    }
}