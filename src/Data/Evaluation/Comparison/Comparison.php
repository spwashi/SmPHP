<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 8:32 PM
 */

namespace Sm\Data\Evaluation\Comparison;


use Sm\Data\Evaluation\StandardEvaluableStatement;

/**
 * Class Comparison
 *
 * Represents evaluable statements that are meant to compare
 *
 * @package Sm\Data\Evaluation\Comparison
 */
abstract class Comparison extends StandardEvaluableStatement {
    private $left;
    private $right;
    /**
     * Comparison constructor.
     *
     * @param $left
     * @param $right
     */
    public function __construct($left = null, $right = null) {
        $this->left  = $left;
        $this->right = $right;
        parent::__construct();
    }
    /**
     * Static constructor for the Comparison class
     *
     * @param mixed|null $left
     * @param mixed|null $right
     *
     * @return static
     */
    public static function init($left = null, $right = null) {
        return new static($left, $right);
    }
    /**
     * Get the Right side of the comparison
     *
     * @return null
     */
    public function getRightSide() {
        return $this->right;
    }
    /**
     * Get the Left side of the comparision
     *
     * @return null
     */
    public function getLeftSide() {
        return $this->left;
    }
    /**
     * Resolve the value of the comparison
     *
     * @return bool
     */
    public function resolve() {
        return $this->evaluate($this->left, $this->right);
    }
}