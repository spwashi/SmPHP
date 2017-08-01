<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 5:25 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Aliasing;


use Sm\Core\Container\Container;
use Sm\Core\Container\Mini\MiniContainer;
use Sm\Core\Exception\Error;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Internal\Identification\Identifiable;
use Sm\Core\Proxy\Proxy;
use Sm\Core\Util;
use Sm\Query\Modules\Sql\Formatting\Aliasing\Exception\InvalidAliasedItem;

/**
 * Class SqlFormattingAliasContainer
 *
 * Container keeping track of the things wi
 *
 * @package Sm\Query\Modules\Sql\Formatting
 */
class SqlFormattingAliasContainer extends Container {
    protected $proxyContainer;
    /**
     * SqlFormattingAliasContainer constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->proxyContainer = new MiniContainer;
    }
    /**
     * Register a Proxy for something that we might alias.
     * This allows us
     *
     * @param                      $item
     * @param  string              $as Classname of how it is supposed to be proxied
     * @param \Sm\Core\Proxy\Proxy $proxy
     *
     * @return $this
     */
    public function registerProxy($item, $as, Proxy $proxy) {
        $this->proxyContainer->register($this->standardizeName($item) . $as, $proxy);
        return $this;
    }
    public function resolveProxy($item, $as) {
        try {
            return $this->proxyContainer->resolve($this->standardizeName($item) . $as);
        } catch (InvalidAliasedItem $exception) {
            return null;
        }
    }
    
    /**
     * @param array|null|string                                  $name
     * @param callable|mixed|null|\Sm\Core\Resolvable\Resolvable $registrand
     *
     * @return $this
     */
    public function register($name = null, $registrand = null) {
        $name = $this->standardizeName($name);
        parent::register($name, $registrand);
        return $this;
    }
    /**
     * @param mixed|null|string $name
     *
     * @return mixed|null
     */
    public function resolve($name = null) {
        $name = $this->standardizeName($name);
        return parent::resolve($name);
    }
    /**
     * If we alias something and that gets aliased, we want to be able to get the last alias of the item.
     *
     * Keep trying to find the alias of something if we can
     *
     * @param string $item
     *
     * @return mixed|null|string
     * @throws \Sm\Core\Exception\Error
     */
    public function getFinalAlias($item) {
        $aliased = $item;
        $count   = 0;
        # Loop through, replacing "next_alias" with the result of "resolve". Stop once there are no more aliases
        try {
            while ($next_alias = $this->resolve($this->standardizeName($aliased))) {
                $count++;
                $aliased = $next_alias;
                if ($count === 15) throw new Error("Looks like there might be some recursion. 15 calls to 'resolve' after [" . Util::getShapeOfItem($aliased) . ']');
            }
        } catch (InvalidArgumentException $e) {
        }
        
        return $aliased;
    }
    protected function standardizeName($name): string {
        if ($name instanceof Identifiable) return $name->getObjectId();
        if (is_string($name) || is_scalar($name)) return "$name";
        throw new Exception\InvalidAliasedItem("There is no way to alias this '" . Util::getShapeOfItem($name) . "' - " . json_encode($name));
    }
}