<?php /** @noinspection ALL */

/**
 * User: Sam Washington
 * Date: 7/24/17
 * Time: 10:00 PM
 */

namespace Sm\Modules\Query\MySql;


use Sm\Core\Module\ModuleContainer;
use Sm\Core\Query\Module\Exception\UnfoundQueryModuleException;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Modules\Query\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Query\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Modules\Query\Sql\Data\Column\IntegerColumnSchema;
use Sm\Modules\Query\Sql\Data\Column\VarcharColumnSchema;
use Sm\Modules\Query\Sql\Formatting\Statements\Exception\MalformedStatementException;
use Sm\Modules\Query\Sql\Statements\CreateTableStatement;
use Sm\Query\Proxy\String_QueryProxy;
use Sm\Query\QueryLayer;
use Sm\Query\Statements\InsertStatement;
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
        $this->module = $module->initialize();
    }
    
    public function testCanInterpretSelect() {
        $result = $this->module->interpret(String_QueryProxy::init('SELECT "hello"'));
        $this->assertInternalType('array', $result);
        $this->assertEquals('hello', $result[0]['hello'] ?? 0);
        
        $query           = StringResolvable::init('hello');
        $selectStatement = SelectStatement::init()->select($query);
        $result_2        = $this->module->interpret($selectStatement);
        
        $this->assertInternalType('array', $result_2);
        $this->assertEquals('hello', $result_2[0]['hello'] ?? 0);
        
        # For an SELECT statement
        $this->expectException(MalformedStatementException::class);
        
        $emptySelectStatement = SelectStatement::init()->select()->from('users');
        $result               = $this->module->interpret($emptySelectStatement);
    }
    public function testCan() {
        $id                   = IntegerColumnSchema::init('id')->setAutoIncrement(true)->setLength(11)->setNullability(0);
        $role                 = VarcharColumnSchema::init('role')->setLength(20)->setNullability(0);
        $primaryKeyConstraint = PrimaryKeyConstraintSchema::init()->addColumn($id);
        $tableName            = 'user_roles';
        $create_table         = CreateTableStatement::init($tableName)->withColumns($id, $role)->withConstraints($primaryKeyConstraint);
        
        echo "--- CREATE TABLE STATEMENT --- \n";
        echo json_encode($create_table, JSON_PRETTY_PRINT);
        echo "\n\n";
        
        try {
            $result = $this->module->interpret($create_table);
            
            echo "--- EXECUTED STATEMENT RESULT --- \n";
            echo json_encode($result, JSON_PRETTY_PRINT);
            echo "\n\n";
            
            $show_tables        = String_QueryProxy::init("SHOW TABLES");
            $show_tables_result = $this->module->interpret($show_tables);
            
            echo "--- SHOW TABLES RESULT --- \n";
            echo json_encode($show_tables_result, JSON_PRETTY_PRINT);
            echo "\n\n";
        } catch (UnfoundQueryModuleException $e) {
        }
    }
    
    public function testGetAllUsers() {
        $select    = SelectStatement::init()->select('*')->from('users');
        $all_users = $this->module->interpret($select);
        var_dump($all_users);
    }
    public function testCreateAllUsers() {
        $select    = InsertStatement::init()->set();
        $all_users = $this->module->interpret($select);
        var_dump($all_users);
    }
}
