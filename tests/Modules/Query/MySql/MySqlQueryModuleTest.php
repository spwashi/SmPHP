<?php
/**
 * User: Sam Washington
 * Date: 7/24/17
 * Time: 10:00 PM
 */

namespace Sm\Modules\Query\MySql;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Module\ModuleContainer;
use Sm\Core\Query\Module\Exception\UnfoundQueryModuleException;
use Sm\Core\Resolvable\Error\UnresolvableException;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Modules\Query\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Modules\Query\Sql\Data\Column\IntegerColumnSchema;
use Sm\Modules\Query\Sql\Formatting\Statements\Exception\MalformedStatementException;
use Sm\Modules\Query\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Query\Sql\Statements\CreateTableStatement;
use Sm\Query\Proxy\String_QueryProxy;
use Sm\Query\QueryLayer;
use Sm\Query\Statements\SelectStatement;

class MySqlQueryModuleTest extends \PHPUnit_Framework_TestCase {
    /** @var  QueryLayer $layer */
    protected $layer;
    /** @var  MySqlQueryModule $module */
    protected $module;
    public function setUp() {
        $layer = new QueryLayer(new ModuleContainer);
        $module       = new MySqlQueryModule;
        $module->registerAuthentication(MySqlAuthentication::init()
                                            ->setCredentials("codozsqq",
                                                             "^bzXfxDc!Dl6",
                                                             "localhost",
                                                             'sm_test'));
        
        $layer->registerQueryModule($module, function () use ($module) { return $module; }, 0);
        $this->layer  = $layer;
        $this->module = $module;
    }
    
    public function testCanInterpretSelect() {
        try {
            $result = $this->layer->interpret(String_QueryProxy::init('SELECT "hello"'));
            $this->assertInternalType('array', $result);
            $this->assertEquals('hello', $result[0]['hello'] ?? 0);
            
            $query           = StringResolvable::init('hello');
            $selectStatement = SelectStatement::init()->select($query);
            $result_2        = $this->layer->interpret($selectStatement);
            
            $this->assertInternalType('array', $result_2);
            $this->assertEquals('hello', $result_2[0]['hello'] ?? 0);
            
            
            # For an empty class
            $this->expectException(MalformedStatementException::class);
            
            $emptySelectStatement = SelectStatement::init()->select()->from('users');
            $this->layer->interpret($emptySelectStatement);
            
        } catch (UnfoundQueryModuleException|InvalidArgumentException|UnresolvableException $e) {
            throw $e;
        }
        
    }
    public function testCan() {
        $id           = IntegerColumnSchema::init('id')->setLength(11)->setNullability(0);
        $create_table = CreateTableStatement::init('std')
                                            ->withColumns($id)
                                            ->withConstraints(PrimaryKeyConstraintSchema::init()
                                                                                        ->addColumn($id));
        $query        = String_QueryProxy::init("SHOW TABLES");
//        $result = $this->layer->interpret($create_table);
        echo json_encode($create_table, JSON_PRETTY_PRINT);
    }
}
