<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 6:39 PM
 */

namespace Sm\Modules\Sql\Formatting;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Core\Formatting\Formatter\FormatterFactory;
use Sm\Core\Util;
use Sm\Modules\Sql\Formatting\Aliasing\Exception\InvalidAliasedItem;
use Sm\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Modules\Sql\Formatting\Proxy\Aliasing\AliasedFormattingProxy;
use Sm\Modules\Sql\Formatting\Proxy\PlaceholderFormattingProxy;

/**
 * Class SqlQueryFormatterFactory
 *
 * @package Sm\Modules\Sql\Formatting
 * @method Formatter resolve($name = null)
 */
class SqlQueryFormatterManager {
    /** @var \Sm\Core\Formatting\Formatter\FormatterFactory $formatterFactory */
    public    $formatterFactory;
    protected $formattingProxyFactory;
    /**
     * @var \Sm\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer
     */
    private $aliasContainer;
    /**
     * @var \Sm\Modules\Sql\Formatting\SqlFormattingContext
     */
    private $formattingContext;
    
    /**
     * SqlQueryFormatterFactory constructor.
     *
     * @param \Sm\Core\Formatting\Formatter\FormatterFactory                  $formatterFactory
     * @param \Sm\Modules\Sql\Formatting\SqlFormattingProxyFactory            $formattingProxyFactory
     * @param \Sm\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer $aliasContainer
     *
     * @internal param \Sm\Modules\Sql\Formatting\SqlFormattingContext $context
     */
    public function __construct(FormatterFactory $formatterFactory = null,
                                SqlFormattingProxyFactory $formattingProxyFactory = null,
                                SqlFormattingAliasContainer $aliasContainer = null) {
        $this->formattingProxyFactory = $formattingProxyFactory ?? new SqlFormattingProxyFactory;
        $this->formatterFactory       = $formatterFactory ?? new FormatterFactory;
        $this->aliasContainer         = $aliasContainer ?? new SqlFormattingAliasContainer;
    }
    public static function init(FormatterFactory $formatterFactory,
                                SqlFormattingProxyFactory $formattingProxyFactory,
                                SqlFormattingAliasContainer $aliasContainer = null,
                                SqlFormattingContext $context = null) {
        return new static(...func_get_args());
    }
    public function build(...$arguments): Formatter {
        $formatter = $this->formatterFactory->build(...$arguments);
        
        if ($formatter instanceof SqlQueryFormatter) {
            $formatter->setFormatterManager($this);
        }
        
        return $formatter;
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
    public function alias($item, string $alias_classname, $alias_name = null) {
        if (!is_a($alias_classname, AliasedFormattingProxy::class, 1)) {
            throw new InvalidArgumentException("can only use AliasedFormattingProxies as aliases");
        }
        
        /** @var \Sm\Modules\Sql\Formatting\Proxy\Aliasing\AliasedFormattingProxy $aliasProxy */
        $aliasProxy = $this->proxy($item, $alias_classname);
        if (!is_null($aliasProxy->getAlias())) {
            return $aliasProxy;
        }
        
        # Create an alias randomly if one was not specified
        if (!$alias_name) $alias_name = Util::generateRandomString(5, Util::ALPHA);
        $aliasProxy->setAlias($alias_name);
        
        //  $name = ($item instanceof Identifiable ? $item->getObjectId() : '') . '|' . $alias_classname;
        
        
        $this->aliasContainer->register($item, $aliasProxy);
        return $aliasProxy;
    }
    public function getFinalAlias($item) {
        return $this->aliasContainer->getFinalAlias($item);
    }
    
    
    /**
     * Return an item Proxied in a certain way
     *
     * @param mixed       $item
     * @param string|null $as
     *
     * @return mixed|null
     */
    public function proxy($item, string $as = null) {
        if ($proxy = $this->aliasContainer->resolveProxy($item, $as)) {
            return $proxy;
        }
        
        if (isset($as)) {
            $proxy = $this->formattingProxyFactory->build($as, $item, $this->formattingProxyFactory);
        } else {
            $proxy = $this->formattingProxyFactory->build($item, $this->formattingProxyFactory);
        }
        
        try {
            $this->aliasContainer->registerProxy($item, $as, $proxy);
        } catch (InvalidAliasedItem $e) {
        } finally {
            return $proxy;
        }
    }
    /**
     * Get the object that will hold all of the Aliases for the FormatterFactory.
     *
     * #todo ideally this would only hold the aliases that are going to be used across the operation.
     *
     * @return \Sm\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer
     */
    public function getAliasContainer(): SqlFormattingAliasContainer {
        return $this->aliasContainer;
    }
    public function getFormattingContext(): ? SqlFormattingContext { return $this->formattingContext; }
    
    /**
     * Creates a placeholder for a Variable's value
     *
     * @param      $value
     * @param null $name
     *
     * @return mixed|null
     */
    public function placeholder($value, $name = null) {
        $name = $name ?? Util::generateRandomString(4, Util::getAlphaCharacters(0));
        if (isset($this->formattingContext)) {
            #todo flag this as being associative or not? nah
            $addedVariables = $name !== false ? [ $name => $value ] : [ $value ];
            $this->formattingContext->addVariables($addedVariables);
        }
        return $this->proxy([ $name, $value, ], PlaceholderFormattingProxy::class);
    }
    public function format($item = null, SqlFormattingContext $sqlFormattingContext = null) {
        $previousFormattingContext = $this->formattingContext;
        $this->formattingContext   = $sqlFormattingContext ?? $previousFormattingContext;
        $result                    = $this->formatterFactory->format($item, $this->formattingContext);
        
        $this->formattingContext = $previousFormattingContext;
        return $result;
    }
    
}