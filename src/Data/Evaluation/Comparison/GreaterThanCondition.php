<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 8:31 PM
 */

namespace Sm\Data\Evaluation\Comparison;

use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Evaluation\TwoOperandStatement;


/**
 * Class GreaterThan
 *
 * Tells whether something is greater than another thing
 *
 * @package Sm\Data\Evaluation\Comparison
 */
class GreaterThanCondition extends Comparison implements TwoOperandStatement {
    public function getOperator(): string {
        return '>';
    }
    /**
     * Decide if item 1 is greater than item 2
     *
     * @inheritdoc
     *
     * @param \Sm\Core\Resolvable\Resolvable[] $items
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function evaluateWithDefault(...$items) {
        if (count($items) !== 2) throw new InvalidArgumentException("Can only compare 2 items");
        return $items[0] > $items[1];
    }
}