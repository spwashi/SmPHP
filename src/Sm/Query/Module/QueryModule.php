<?php
/**
 * User: Sam Washington
 * Date: 7/23/17
 * Time: 7:41 PM
 */

namespace Sm\Query\Module;


use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Context\Layer\Module\LayerModule;
use Sm\Core\Module\Error\IncompleteModuleException;
use Sm\Query\Interpretation\QueryInterpreter;
use Sm\Query\QueryLayer;

/**
 * Class QueryModule
 *
 * @package Sm\Query\Module
 */
abstract class QueryModule extends LayerModule implements QueryInterpreter {
    const MONITOR__QUERY_MODULE = 'query_module_monitor';
    
    /** @var  string */
    protected $queryType;
    /** @var  QueryInterpreter */
    protected $queryInterpreter;
    public function getQueryType(): string {
        if (!isset($this->queryType)) throw new IncompleteModuleException("Cannot register a QueryModule without a queryType set.");
        return $this->queryType;
    }
    /**
     * Provided some components of a Query, interpret what the result would yield
     *
     * @param                              $query
     * @param \Sm\Core\Context\Layer\Layer $layer
     *
     * @return mixed
     */
    abstract public function interpret($query);
    abstract public function canConnect(): bool;
    /**
     * @param QueryLayer|Layer $context
     *
     * @throws \Sm\Core\Context\Exception\InvalidContextException
     */
    protected function establishContext(Layer $context = null) {
        /** @var QueryLayer $context */
        if (isset($context) && !($context instanceof QueryLayer)) {
            throw new InvalidContextException("Cannot register on anything but a QueryLayer.");
        }
        parent::establishContext($context);
    }
}