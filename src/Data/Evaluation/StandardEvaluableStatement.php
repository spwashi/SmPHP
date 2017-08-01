<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 12:22 PM
 */

namespace Sm\Data\Evaluation;


use Sm\Core\Exception\TypeMismatchException;
use Sm\Core\Factory\Exception\FactoryCannotBuildException;
use Sm\Core\Factory\Factory;
use Sm\Core\Factory\StandardFactory;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Util;

abstract class StandardEvaluableStatement implements EvaluableStatement {
    use HasObjectIdentityTrait;
    /** @var  Factory $evaluatorFactory The thing that we are going to use to evaluate statements of this kind */
    private $evaluatorFactory;
    public function __construct() {
        $this->createSelfID();
    }
    /**
     * @param callable|\Sm\Core\Resolvable\FunctionResolvable $evaluator
     *
     * @param string                                          $name If we are going to name the evaluator (just like any other factory), do it here
     *
     * @return mixed
     */
    public function registerEvaluator(callable $evaluator, $name = null) {
        $evaluatorFactory = $this->getEvaluatorFactory();
        if ($name) {
            $evaluatorFactory->register($name, $evaluator);
        } else {
            $evaluatorFactory->register($evaluator);
        }
        return $this;
    }
    /**
     * Come up with a value for the class
     *
     * @return mixed
     * @throws \Sm\Core\Exception\TypeMismatchException
     */
    public function evaluate() {
        $arguments = func_get_args();
        try {
            # Get the evaluator that we would use to evaluate these arguments.
            # This gives us a bit more flexibility to decide how things get formatted
            $evaluator = $this->getEvaluatorFactory()->build(...$arguments);
            if (!($evaluator instanceof Resolvable) && isset($evaluator)) {
                throw new TypeMismatchException("Evaluator should have been a Resolvable - " . Util::getShapeOfItem($evaluator) . " given");
            }
            return $evaluator->resolve(...$arguments);
        } catch (FactoryCannotBuildException $e) {
            return $this->evaluateWithDefault(...$arguments);
        }
    }
    /**
     * This uses the class's default evaluator to come up with a value
     *
     * @param \Sm\Core\Resolvable\Resolvable[] $items
     *
     * @return mixed
     */
    abstract protected function evaluateWithDefault();
    
    protected function getEvaluatorFactory(): Factory {
        if (!isset($this->evaluatorFactory)) $this->setEvaluatorFactory();
        return $this->evaluatorFactory;
    }
    /**
     * Set the evaluatorFactory of this object
     *
     * @param Factory $evaluatorFactory
     *
     * @return StandardEvaluableStatement
     */
    public function setEvaluatorFactory(Factory $evaluatorFactory = null): StandardEvaluableStatement {
        $this->evaluatorFactory =
            !$evaluatorFactory && !isset($this->evaluatorFactory)
                ? new StandardFactory
                : $evaluatorFactory;
        return $this;
    }
}