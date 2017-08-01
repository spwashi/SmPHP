<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 9:19 PM
 */

namespace Sm\Data\Evaluation;


/**
 * Interface TwoOperandStatement
 *
 * Represents statements that take two operands
 *
 * @package Sm\Data\Evaluation
 */
interface TwoOperandStatement {
    /**
     * Get the Operator that will be used to join
     *
     * @return string
     */
    public function getOperator(): string;
    /**
     * Get the thing that is going to be on the left side of the operator
     *
     * @return mixed
     */
    public function getLeftSide();
    /**
     * Get the thing that is going to be on the right side of the operator
     *
     * @return mixed
     */
    public function getRightSide();
}