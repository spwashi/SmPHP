<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 7:35 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing;


/**
 * Class IsAliasedFormattingProxyTrait
 *
 * For Formatting Proxies that act as Aliases
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing
 */
trait IsAliasedFormattingProxyTrait {
    protected $alias;
    /**
     * Set the Alias of the FormattingProxy
     *
     * @param $alias
     *
     * @return $this
     */
    public function setAlias(string $alias) {
        $this->alias = $alias;
        return $this;
    }
}