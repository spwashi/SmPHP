<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 5:54 PM
 */

namespace Sm\Modules\Sql\Formatting;


use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Modules\Sql\Formatting\Proxy\Aliasing\AliasedFormattingProxy;

/**
 * Class SqlQueryFormatter
 *
 * Given a Statement, Clause, or whatever we call Queries,
 * return a string representation of the Query
 *
 * @package Sm\Modules\Sql
 */
class SqlQueryFormatter implements Formatter {
    /** @var  \Sm\Modules\Sql\Formatting\SqlQueryFormatterManager $formatterManager */
    protected $formatterManager;
    /** @var  SqlFormattingAliasContainer $aliasContainer */
    protected $aliasContainer;
    /**
     * SqlQueryFormatter constructor.
     *
     * @param \Sm\Modules\Sql\Formatting\SqlQueryFormatterManager $formatterManager
     */
    public function __construct(SqlQueryFormatterManager $formatterManager) {
        $this->formatterManager = $formatterManager;
    }
    public static function init(SqlQueryFormatterManager $formatterManager = null) {
        if (!isset($formatterManager)) $formatterManager = new SqlQueryFormatterManager;
        return new static($formatterManager);
    }
    /**
     * Return the item Formatted in the specific way
     *
     * @param $item
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function format($item): string {
        return $this->formatComponent($item);
    }
    
    
    /**
     * Do any necessary aliasing before formatting this item
     *
     * @param $item
     */
    public function prime($item) { }
    /**
     * Create a Proxy so we can interact with a component of this Formatter's process as it would exist within a certain context
     *
     * @param $item
     * @param $as
     *
     * @return mixed|null
     */
    public function proxy($item, $as) {
        return $this->formatterManager->proxy($item, $as);
    }
    /**
     * Set the AliasContainer that will be used by this Formatter
     *
     * @param \Sm\Modules\Sql\Formatting\SqlQueryFormatterManager $formatterManager
     *
     * @return $this
     */
    public function setFormatterManager(SqlQueryFormatterManager $formatterManager) {
        $this->formatterManager = $formatterManager;
        return $this;
    }
    
    /**
     * Alias an item, using the classname provided as the AliasedFormattingProxy we will wrap the item around
     *
     * @param        $item
     * @param string $alias_classname MUST BE AN AliasedFormattingProxy classname. This is what we will use to hold the Alias
     * @param null   $alias_name
     *
     * @return AliasedFormattingProxy
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function alias($item, string $alias_classname, $alias_name = null) {
        return $this->formatterManager->alias($item, $alias_classname, $alias_name);
    }
    protected function getFinalAlias($item) {
        return $this->formatterManager->getFinalAlias($item);
    }
    /**
     * Format something used by
     *
     * @param $component
     *
     * @return mixed
     */
    protected function formatComponent($component) {
        $formatter = $this->buildComponentFormatter($component);
        return $formatter->format($component);
    }
    protected function primeComponent($component) {
        $formatter = $this->buildComponentFormatter($component);
        if ($formatter instanceof SqlQueryFormatter) $formatter->prime($component);
    }
    /**
     * @param $component
     *
     * @return Formatter
     */
    protected function buildComponentFormatter($component): Formatter {
        $formatter = $this->formatterManager->build($component);
        
        return $formatter;
    }
}