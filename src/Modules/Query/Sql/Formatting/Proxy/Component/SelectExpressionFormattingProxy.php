<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 8:56 PM
 */

namespace Sm\Modules\Query\Sql\Formatting\Proxy\Component;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Util;
use Sm\Data\Source\Schema\DataSourceSchema;
use Sm\Data\Source\Schema\NamedDataSourceSchema;
use Sm\Modules\Query\Sql\Formatting\Proxy\Aliasing\AliasedSourceFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\Proxy\Source\NamedDataSourceFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\Proxy\SqlFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\SqlFormattingProxyFactory;

/**
 * Class SelectExpressionFormattingProxy
 *
 * Meant to help format SELECT expresions
 *
 * @package Sm\Modules\Query\Sql\Formatting\Proxy\Component
 */
class SelectExpressionFormattingProxy extends SqlFormattingProxy {
    protected $name;
    /** @var  NamedDataSourceFormattingProxy $subject */
    protected $subject;
    protected $alias;
    /**
     * SelectExpressionFormattingProxy constructor.
     *
     * @param NamedDataSourceFormattingProxy                       $subject
     * @param \Sm\Modules\Query\Sql\Formatting\SqlFormattingProxyFactory $formattingProxyFactory
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function __construct($subject, SqlFormattingProxyFactory $formattingProxyFactory = null) {
        if (is_array($subject)) {
            if (count($subject) !== 2) {
                throw new InvalidArgumentException("Can only accept arrays with 2 arguments like [source,alias]");
            }
            if (!(($subject[0]??null) instanceof DataSourceSchema) || !(($subject[1]??null) instanceof NamedDataSourceSchema)) {
                throw new InvalidArgumentException("Can only accept arrays like[source,alias]\n" . Util::getShape($subject) . ' given');
            }
            list($subject, $this->alias) = $subject;
        } else if (!($subject instanceof DataSourceSchema)) {
            throw new InvalidArgumentException("Can only initialize with DataSourceSchemas [" . Util::getShape($subject) . '] provided.');
        }
        parent::__construct($subject, $formattingProxyFactory);
    }
    public function getSource() {
        if ($this->subject instanceof AliasedSourceFormattingProxy) return $this->subject->getOriginal();
        return $this->subject;
    }
    public function getAlias() {
        if (isset($this->alias)) return $this->alias;
        if ($this->subject instanceof AliasedSourceFormattingProxy) return $this->alias = $this->subject->getName();
        return null;
    }
    
}