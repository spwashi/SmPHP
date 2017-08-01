<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 8:12 AM
 */

namespace Sm\Data\Evaluation;


use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\Resolvable;

/**
 * Class EvaluableStatement
 *
 * Represents a statement that can be evaluated to one discrete value
 *
 * @package Sm\Data\Evaluation
 */
interface EvaluableStatement extends Resolvable {
    /**
     * Register something as being able to evaluate this Statement
     *
     * @param callable|FunctionResolvable $evaluator
     *
     * @return mixed
     */
    public function registerEvaluator(callable $evaluator);
}