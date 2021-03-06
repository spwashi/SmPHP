<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 8:45 PM
 */

namespace Sm\Modules\Query\Sql\Formatting\Proxy\Aliasing;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Util;
use Sm\Data\Source\Schema\DataSourceSchema;
use Sm\Modules\Query\Sql\Formatting\Proxy\Source\NamedDataSourceFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\SqlFormattingProxyFactory;

/**
 * Class TableAliasProxy
 *
 * Represents an Aliased Table
 *
 * @package Sm\Modules\Query\Sql\Formatting\Proxy\Aliasing
 * @method  AliasedSourceFormattingProxy static init(...$items)
 */
class AliasedSourceFormattingProxy extends NamedDataSourceFormattingProxy implements AliasedFormattingProxy {
    use IsAliasedFormattingProxyTrait;
    
    /** @var  \Sm\Modules\Query\Sql\Formatting\Proxy\Source\NamedDataSourceFormattingProxy */
    protected $subject;
    /**
     * AliasedTableFormattingProxy constructor.
     *
     * @param                                                            $subject
     * @param \Sm\Modules\Query\Sql\Formatting\SqlFormattingProxyFactory       $formattingProxyFactory
     *
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function __construct($subject, SqlFormattingProxyFactory $formattingProxyFactory = null) {
        if (!($subject instanceof DataSourceSchema)) throw new UnimplementedError("Can only alias Sources. [" . Util::getShape($subject) . '] provided');
        parent::__construct($subject, $formattingProxyFactory);
    }
    public function getAlias():?string {
        return $this->alias;
    }
    public function getName(): ?string {
        return $this->getAlias();
    }
    public function getOriginal(): DataSourceSchema {
        return $this->subject;
    }
}