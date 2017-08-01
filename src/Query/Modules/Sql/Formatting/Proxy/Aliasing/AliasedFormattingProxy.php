<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 8:20 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing;


/**
 * Interface AliasedFormattingProxy
 *
 * Represents FormattingProxies that act as Aliases for other things
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing
 */
interface AliasedFormattingProxy {
    public function setAlias(string $alias);
    public function getAlias();
    public function getOriginal();
}