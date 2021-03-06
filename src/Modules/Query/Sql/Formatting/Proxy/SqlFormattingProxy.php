<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 8:43 AM
 */

namespace Sm\Modules\Query\Sql\Formatting\Proxy;


use Sm\Core\Formatting\Formatter\FormattingProxyFactory;
use Sm\Core\Formatting\FormattingProxy;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;
use Sm\Modules\Query\Sql\Formatting\Proxy\Exception\MissingFormattingFactoryException;
use Sm\Modules\Query\Sql\Formatting\SqlFormattingProxyFactory;

/**
 * Class SqlFormattingProxy
 *
 * Represents FormattingProxies for Sql things
 *
 * @package Sm\Modules\Query\Sql\Formatting\Proxy
 */
abstract class SqlFormattingProxy implements FormattingProxy, Identifiable {
    use HasObjectIdentityTrait;
    protected $subject;
    /** @var SqlFormattingProxyFactory */
    private $formattingProxyFactory;
    public function __construct($subject, SqlFormattingProxyFactory $formattingProxyFactory = null) {
        $this->subject                = $subject;
        $this->formattingProxyFactory = $formattingProxyFactory;
        $this->createSelfID();
    }
    /**
     * Static constructior
     *
     * @param                                                      $subject
     * @param \Sm\Core\Formatting\Formatter\FormattingProxyFactory $formattingProxyFactory
     *
     * @return static
     */
    public static function init($subject, FormattingProxyFactory $formattingProxyFactory) {
        return new static(...func_get_args());
    }
    public function getFormattingProxyFactory(): SqlFormattingProxyFactory {
        if (!isset($this->formattingProxyFactory)) throw new MissingFormattingFactoryException;
        return $this->formattingProxyFactory;
    }
    public function getSubject() {
        return $this->subject;
    }
}