<?php
/**
 * User: Sam Washington
 * Date: 7/24/17
 * Time: 10:00 PM
 */

namespace Sm\Modules\Sql\MySql;


use Sm\Core\Module\ModuleContainer;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Modules\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Modules\Sql\Data\Column\IntegerColumnSchema;
use Sm\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Sql\MySql\Module\MySqlQueryModule;
use Sm\Modules\Sql\Statements\CreateTableStatement;
use Sm\Query\Proxy\String_QueryProxy;
use Sm\Query\QueryLayer;
use Sm\Query\Statements\SelectStatement;

class MySqlQueryModuleTest extends \PHPUnit_Framework_TestCase {
    /** @var  QueryLayer $layer */
    protected $layer;
    /** @var  MySqlQueryModule $module */
    protected $module;
    public function setUp() {
        $layer  = new QueryLayer(new ModuleContainer);
        $module = new MySqlQueryModule;
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
        $result = $this->layer->interpret(String_QueryProxy::init('SELECT "hello"'));
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['hello'] ?? 0, 'hello');
        $result = $this->layer->interpret(SelectStatement::init()->select(StringResolvable::init('hello')));
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['hello'] ?? 0, 'hello');
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
