<?php
/**
 * User: Sam Washington
 * Date: 7/24/17
 * Time: 12:28 PM
 */

namespace Sm\Modules\Sql\MySql\Module;


use Sm\Core\Context\Context;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Module\ModuleProxy;
use Sm\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Modules\Sql\Formatting\SqlFormattingProxyFactory;
use Sm\Modules\Sql\Formatting\SqlQueryFormatterManager;
use Sm\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Sql\MySql\MySqlQueryInterpreter;
use Sm\Query\Module\QueryModule;
use Sm\Query\Proxy\QueryProxy;
use Sm\Query\Statements\QueryComponent;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * Class MySqlQueryModule
 *
 * @method MySqlQueryModuleProxy initialize(Context $context = null): ?MySqlQueryModuleProxy
 */
class MySqlQueryModule extends QueryModule {
    const MYSQL = 'mysql';
    protected $queryType = 'mysql';
    /** @var string $config_path The path from which we will load the Module configuration */
    protected $config_path = '_config/mysql.query.module.sm.php';
    /** @var  SqlQueryFormatterManager $queryFormatter */
    protected $queryFormatter;
    private   $authentication;
    /**
     * @return \Sm\Modules\Sql\MySql\Module\MySqlQueryModule
     */
    public static function init() { return new static(...func_get_args()); }
    
    public function registerAuthentication(MySqlAuthentication $mySqlAuthentication) {
        $this->authentication = $mySqlAuthentication;
        return $this;
    }
    public function interpret(Layer $layer = null, $query, MySqlAuthentication $authentication = null) {
        if (!($query instanceof QueryComponent) && !($query instanceof QueryProxy)) {
            throw new InvalidArgumentException("Can only query on components or proxies");
        }
        $queryInterpreter = new MySqlQueryInterpreter($authentication ?? $this->authentication,
                                                      $this->getQueryFormatter($layer));
        return $queryInterpreter->interpret($query);
    }
    
    /**
     * @param \Sm\Core\Context\Layer\Layer|null $context
     *
     * @return null|\Sm\Modules\Sql\Formatting\SqlQueryFormatterManager
     */
    public function getQueryFormatter(Layer $context = null): ? SqlQueryFormatterManager {
        if (isset($context)) return $this->getContextRegistry($context)->resolve('queryFormatter');
        return $this->queryFormatter;
    }
    /**
     * Set the SqlQueryFormatterFactory that will be responsible for formatting Queries on this Module
     *
     * @param SqlQueryFormatterManager     $queryFormatter The SqlQueryFormatterFactory that is going to be responsible for formatting the queries on this layer.
     * @param \Sm\Core\Context\Layer\Layer $context        The Layer on which we are registering it. If not specified, just registered on the class.
     *
     * @return \Sm\Modules\Sql\MySql\MySqlQueryModule
     */
    public function setQueryFormatter(SqlQueryFormatterManager $queryFormatter, Layer $context = null): MySqlQueryModule {
        if (!isset($context)) {
            $this->queryFormatter = $queryFormatter;
        } else {
            $this->getContextRegistry($context)->register('queryFormatter', $queryFormatter);
        }
        return $this;
    }
    protected function createModuleProxy(Context $context = null): ModuleProxy {
        return new MySqlQueryModuleProxy($this, $context);
    }
    protected function _initialize(Layer $context = null) {
        if ($context) parent::_initialize($context);
        $this->initializeFormatter($context);
    }
    
    protected function initializeFormatter(Layer $context = null) {
        $queryFormatter = $this->getQueryFormatter($context);
        if (isset($queryFormatter)) return;
        
        # Includes functions to load configuration into both the proxy_handlers and the formatting_handlers
        require_once $this->config_path;
        
        # So we know how to convert things into proxies
        $formattingProxyFactory = SqlFormattingProxyFactory::init();
        if (function_exists('register_proxy_handlers')) {
            register_proxy_handlers($formattingProxyFactory);
        }
        
        # So we know how to format queries
        # todo dependency injection?
        $queryFormatter = new SqlQueryFormatterManager(null, $formattingProxyFactory, SqlFormattingAliasContainer::init());
        if (function_exists('register_formatting_handlers')) {
            register_formatting_handlers($queryFormatter);
        }
        
        #
        $this->setQueryFormatter($queryFormatter, $context);
    }
}