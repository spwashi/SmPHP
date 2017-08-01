<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 3:38 PM
 */

namespace Sm\Query;


use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\Module\Module;
use Sm\Core\Module\ModuleContainer;
use Sm\Core\Query\Module\Exception\UnfoundQueryModuleException;
use Sm\Query\Module\QueryModule;
use Sm\Query\Module\QueryModuleFactory;

/**
 * Class QueryLayer
 *
 * @package Sm\Query
 */
class QueryLayer extends StandardLayer {
    /** @var  QueryModuleFactory $queryModuleFactory */
    private $queryModuleFactory;
    public function __construct(ModuleContainer $moduleContainer, QueryModuleFactory $queryModuleFactory = null) {
        parent::__construct($moduleContainer);
        $this->queryModuleFactory = $queryModuleFactory ?? new QueryModuleFactory;
    }
    /**
     * Interpret Queries
     *
     * @param \Sm\Query\Statements\QueryComponent|\Sm\Query\Proxy\QueryProxy $query
     * @param string                                                         $interpreter The name or identifier of the QueryModule to use (e.g. mysql)
     *
     * @return mixed
     * @throws \Sm\Core\Query\Module\Exception\UnfoundQueryModuleException
     */
    public function interpret($query, string $interpreter = null) {
        /** @var QueryModule $queryModule */
        $queryModule = $this->queryModuleFactory->build($interpreter, $query);
        #@todo resolve queryModule based on index
        if (!$queryModule) throw new UnfoundQueryModuleException("No QueryModule enabled to handle this kind of query.");
        return $queryModule->interpret($this, $query);
    }
    /**
     * Register a QueryModule on this layer.
     *
     * @param \Sm\Query\Module\QueryModule $queryModule
     * @param null                         $factoryMethod Following typical factories, this is a method that will belong to the queryModuleFactory
     *                                                    to resolve the QueryModule that would best execute this query
     * @param bool                         $do_name       Should we add the method to the factory with a name? If we do, the method will only be
     *                                                    accessible by name. If not, the method will only be accessible without a name.
     */
    public function registerQueryModule(QueryModule $queryModule, $factoryMethod = null, $do_name = true) {
        $this->registerModule($queryModule->getQueryType(), $queryModule);
        if (isset($factoryMethod)) {
            # arguments like [query_type, factoryMethod]
            $args = [
                # null indicates that this is to be run in the default resolution stack
                # if there is no other suitable factory method. Called among the last
                $do_name ? $queryModule->getQueryType() : null,
                
                # Resolution
                $factoryMethod,
            ];
            
            $this->queryModuleFactory->register(...$args);
        }
    }
    /**
     * @param                        $name
     * @param \Sm\Core\Module\Module $module
     *
     * @return bool
     * @throws \Sm\Core\Module\Error\InvalidModuleException
     */
    public function checkCanRegisterModule($name, Module $module) {
        return true;
    }
    
}