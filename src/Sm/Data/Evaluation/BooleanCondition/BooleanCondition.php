<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 8:15 AM
 */

namespace Sm\Data\Evaluation\BooleanCondition;


use Sm\Core\Resolvable\Error\UnresolvableException;
use Sm\Core\Resolvable\Resolvable;
use Sm\Data\Evaluation\StandardEvaluableStatement;

/**
 * Class BooleanCondition
 *
 * Takes multiple inputs, returns a bool depending on the utility of the class
 *
 * @package Sm\Data\Evaluation\Constructs
 */
abstract class BooleanCondition extends StandardEvaluableStatement {
    /** @var Resolvable[] The things that are going to be evaluated */
    protected $items = [];
    
    /**
     * ChainableBooleanEvaluableStatement constructor.
     *
     * @param \Sm\Core\Resolvable\Resolvable[] ...$items
     */
    public function __construct(Resolvable...$items) {
        $this->append(...$items);
        parent::__construct();
    }
    public function resolve(): bool {
        if (!count($this->items)) throw new UnresolvableException("No items to operate on.");
        return $this->evaluate($this->items);
    }
    /**
     * Add items to this Construct
     *
     * @param array ...$items
     *
     * @return $this
     */
    public function append(Resolvable ...$items) {
        $this->items = array_merge($this->items, $items);
        return $this;
    }
    /**
     * @return \Sm\Core\Resolvable\Resolvable[]
     */
    public function getItems(): array {
        return $this->items;
    }
}