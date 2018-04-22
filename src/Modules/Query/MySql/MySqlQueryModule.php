<?php
/**
 * User: Sam Washington
 * Date: 7/24/17
 * Time: 12:28 PM
 */

namespace Sm\Modules\Query\MySql;


use Sm\Core\Context\Context;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Internal\Monitor\Monitored;
use Sm\Modules\Query\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Query\MySql\Interpretation\MySqlQueryInterpreter;
use Sm\Modules\Query\MySql\Proxy\MySqlQueryModuleProxy;
use Sm\Modules\Query\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Modules\Query\Sql\Formatting\SqlFormattingProxyFactory;
use Sm\Modules\Query\Sql\Formatting\SqlQueryFormatterManager;
use Sm\Query\Module\QueryModule;
use Sm\Query\Proxy\QueryProxy;
use Sm\Query\Statements\QueryComponent;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * Class MySqlQueryModule
 *
 * @method MySqlQueryModuleProxy initialize(Context $context = null): ?MySqlQueryModuleProxy
 */
class MySqlQueryModule extends QueryModule implements Monitored {
    use HasMonitorTrait;
    
    const MYSQL = 'mysql';
    protected $queryType = 'mysql';
    /** @var string $config_path The path from which we will load the Module configuration */
    protected $config_path = '_config/mysql.query.module.sm.php';
    /** @var  SqlQueryFormatterManager $queryFormatter */
    protected $queryFormatter;
    private   $authentication;
    
    
    /**
     * @return \Sm\Modules\Query\MySql\MySqlQueryModule
     */
    public static function init() { return new static(...func_get_args()); }
    
    public function registerAuthentication(MySqlAuthentication $mySqlAuthentication) {
        $this->authentication = $mySqlAuthentication;
        return $this;
    }
    /**
     * Interpret a mysql query
     *
     * @param      $query
     * @param null $return_type
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function interpret($query, $return_type = null) {
        if (!($query instanceof QueryComponent) && !($query instanceof QueryProxy)) {
            throw new InvalidArgumentException("Can only query on components or proxies");
        }
        $queryInterpreter = new MySqlQueryInterpreter($this->getAuthentication(), $this->getQueryFormatter());
        $result           = $queryInterpreter->interpret($query, $return_type);
        $this->getMonitor(MySqlQueryInterpreter::MONITOR__QUERY_EXECUTED)
             ->append(...$queryInterpreter->getQueryMonitor()->dump());
        return $result;
    }
    public function getQueryFormatter(): ?SqlQueryFormatterManager {
        return $this->queryFormatter;
    }
    /**
     * Set the SqlQueryFormatterFactory that will be responsible for formatting Queries on this Module
     *
     * @param SqlQueryFormatterManager     $queryFormatter The SqlQueryFormatterFactory that is going to be responsible for formatting the queries on this layer.
     * @param \Sm\Core\Context\Layer\Layer $context        The Layer on which we are registering it. If not specified, just registered on the class.
     *
     * @return \Sm\Modules\Query\MySql\MySqlQueryModule
     */
    public function setQueryFormatter(SqlQueryFormatterManager $queryFormatter): MySqlQueryModule {
        $this->queryFormatter = $queryFormatter;
        return $this;
    }
    protected function establishContext(Layer $context = null) {
        if ($context) parent::establishContext($context);
        $this->initializeFormatter();
    }
    protected function initializeFormatter() {
        $queryFormatter = $this->getQueryFormatter();
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
        $this->setQueryFormatter($queryFormatter);
    }
    protected function getAuthentication(): MySqlAuthentication {
        return $this->authentication;
    }
}