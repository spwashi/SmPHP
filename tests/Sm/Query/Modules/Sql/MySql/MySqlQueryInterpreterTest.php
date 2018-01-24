<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 8:37 PM
 */

namespace Sm\Modules\Sql\MySql;


use Sm\Core\Formatting\Formatter\FormatterFactory;
use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Source\Constructs\JoinedSourceSchematic;
use Sm\Data\Source\Database\DatabaseSource;
use Sm\Data\Source\Database\Table\TableSource;
use Sm\Data\Source\Database\Table\TableSourceSchematic;
use Sm\Modules\Sql\Constraints\ForeignKeyConstraintSchema;
use Sm\Modules\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Modules\Sql\Constraints\UniqueKeyConstraintSchema;
use Sm\Modules\Sql\Data\Column\DateTimeColumnSchema;
use Sm\Modules\Sql\Data\Column\IntegerColumnSchema;
use Sm\Modules\Sql\Data\Column\VarcharColumnSchema;
use Sm\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Modules\Sql\Formatting\SqlFormattingProxyFactory;
use Sm\Modules\Sql\Formatting\SqlQueryFormatterManager;
use Sm\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Sql\MySql\Module\MySqlQueryModule;
use Sm\Modules\Sql\SqlExecutionContext;
use Sm\Modules\Sql\Statements\AlterTableStatement;
use Sm\Modules\Sql\Statements\CreateTableStatement;
use Sm\Query\Statements\InsertStatement;
use Sm\Query\Statements\SelectStatement;
use Sm\Query\Statements\UpdateStatement;


const DO_ECHO_RESULTS = true;
class _Queries {
    /** @var  IntegerColumnSchema */
    protected $user__id;
    /** @var  VarcharColumnSchema */
    protected $user__email_address;
    /** @var  VarcharColumnSchema */
    protected $user__firstName;
    /** @var  VarcharColumnSchema */
    protected $user__lastName;
    /** @var  DateTimeColumnSchema */
    protected $user__update_dt;
    /** @var  DateTimeColumnSchema */
    protected $user__creation_dt;
    
    /** @var  TableSource $table__user */
    protected $table__user;
    /** @var  TableSource $table__clients */
    protected $table__clients;
    protected $clients__creation_dt;
    protected $clients__update_dt;
    protected $clients__client_id;
    protected $clients__user_id;
    protected $clients__id;
    protected $clients__note;
    
    /** @var  \Sm\Modules\Sql\Formatting\SqlQueryFormatterManager $formatterManager */
    private $formatterManager;
    
    #
    ##
    #
    
    public function __construct(SqlQueryFormatterManager $formatterManager) {
        $this->formatterManager = $formatterManager;
        $databaseSource         = new DatabaseSource('Database');
        #
        $this->table__user = new TableSource('users', $databaseSource);
        #
        ##  COLUMNS (USER)
        #
        $this->user__id            = IntegerColumnSchema::init('id')
                                                        ->setAutoIncrement()
                                                        ->setLength(10)
                                                        ->setTableSchema($this->table__user);
        $this->user__email_address = VarcharColumnSchema::init('email_address')
                                                        ->setNullability(0)
                                                        ->setLength(255)
                                                        ->setTableSchema($this->table__user);
        $this->user__firstName     = VarcharColumnSchema::init('first_name')
                                                        ->setLength(255)
                                                        ->setTableSchema($this->table__user);
        $this->user__lastName      = VarcharColumnSchema::init()
                                                        ->setLength(255)
                                                        ->setName('last_name')
                                                        ->setTableSchema($this->table__user);
        $this->user__update_dt     = DateTimeColumnSchema::init('update_dt')
                                                         ->setOnUpdate(DateTimeColumnSchema::CURRENT_TIMESTAMP)
                                                         ->setDefault(null)
                                                         ->setTableSchema($this->table__user);
        $this->user__creation_dt   = DateTimeColumnSchema::init('creation_dt')
                                                         ->setDefault(DateTimeColumnSchema::CURRENT_TIMESTAMP)
                                                         ->setTableSchema($this->table__user);
        #
        ##  COLUMNS (CLIENTS)
        #
        $this->table__clients     = new TableSource('clients', $databaseSource);
        $this->clients__id        = IntegerColumnSchema::init('id')
                                                       ->setAutoIncrement()
                                                       ->setLength(10)
                                                       ->setTableSchema($this->table__clients);
        $this->clients__user_id   = IntegerColumnSchema::init('user_id')
                                                       ->setLength(10)
                                                       ->setTableSchema($this->table__clients);
        $this->clients__client_id = IntegerColumnSchema::init('client_id')
                                                       ->setLength(10)
                                                       ->setTableSchema($this->table__clients);
        
        $this->clients__note = VarcharColumnSchema::init('note')
                                                  ->setLength(255)
                                                  ->setTableSchema($this->table__clients);
        
        $this->clients__update_dt   = DateTimeColumnSchema::init('update_dt')
                                                          ->setOnUpdate(DateTimeColumnSchema::CURRENT_TIMESTAMP)
                                                          ->setDefault(null)
                                                          ->setTableSchema($this->table__clients);
        $this->clients__creation_dt = DateTimeColumnSchema::init('creation_dt')
                                                          ->setOnUpdate(DateTimeColumnSchema::CURRENT_TIMESTAMP)
                                                          ->setDefault(null)
                                                          ->setTableSchema($this->table__clients);
        
    }
    
    public function select1() {
        $statement = SelectStatement::init(/*'user.' . $this->user__email_address->getName(),*/
            $this->user__id,
            $this->user__firstName,
            $this->user__lastName,
            $this->user__email_address->getName())
                                    ->from(JoinedSourceSchematic::init()
                                                                ->setOriginSources($this->table__user)
                                                                ->setJoinConditions(EqualToCondition::init($this->user__id, $this->clients__user_id))
                                                                ->setJoinedSources($this->table__clients))//                               ->where(EqualToCondition::init($this->user__id, 1))
        ;
        
        $result = $this->formatterManager->format($statement, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
        return $statement;
    }
    public function update1() {
        $updateStatement = UpdateStatement::init([
                                                     $this->user__firstName->getName() => 'FIRST_NAME',
                                                     $this->user__lastName->getName()  => 'LAST_NAME',
                                                 ])
                                          ->inSources($this->table__user)
                                          ->where(EqualToCondition::init($this->user__id, 2));
        $result          = $this->formatterManager->format($updateStatement, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
        
        return $updateStatement;
    }
    public function insert1() {
        $bran            = [
            'first_name'    => 'Bran',
            'last_name'     => 'Washington',
            'email_address' => 'bran@spwashi.com',
        ];
        $moonshine       = [
            'first_name'    => 'Moonshine',
            'last_name'     => 'Hickins',
            'email_address' => 'hickins@spwashi.com',
        ];
        $harold          = [
            'first_name'    => 'Harold',
            'last_name'     => 'Washington',
            'email_address' => 'hickins@spwashi.com',
        ];
        $sam             = [
            'first_name'    => 'Samuel',
            'last_name'     => 'Washington',
            'email_address' => 'sam@spwashi.com',
        ];
        $insertStatement = InsertStatement::init()
                                          ->set($bran,
                                                $moonshine,
                                                $harold,
                                                $sam)
                                          ->inSources($this->table__user);
        $result          = $this->formatterManager->format($insertStatement, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
        return $insertStatement;
    }
    public function createTables() {
        $result = [ static::createTable_users(), static::createTable_clients() ];
        
        if (DO_ECHO_RESULTS) {
            echo __FILE__;
            foreach ($result as $item) {
                echo "\n--\n$item\n\n";
            }
        }
        
        return $result;
    }
    public function testAlterTable() {
        $iColumn1 = IntegerColumnSchema::init('one_thing')
                                       ->setAutoIncrement()
                                       ->setLength(10);
        $otherr   = IntegerColumnSchema::init('another_one_thing')
                                       ->setLength(10)
                                       ->setTableSchema(TableSourceSchematic::init('other_tablename'));
        
        
        $alterTableStatement = AlterTableStatement::init('TableName')
                                                  ->withConstraints(ForeignKeyConstraintSchema::init()
                                                                                              ->setConstraintName('table_table_id')
                                                                                              ->addColumn($iColumn1)
                                                                                              ->addRefeferencedColumns($otherr));
        $result              = $this->formatterManager->format($alterTableStatement, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
    }
    public function createTable_users(): string {
        # todo
        #        ->setTableSchema(TableSourceSchematic::init('other_tablename'));
        
        $createTableStatement = CreateTableStatement::init($this->table__user->getName())
                                                    ->withColumns($this->user__id,
        
                                                                  $this->user__firstName,
                                                                  $this->user__lastName,
        
                                                                  $this->user__email_address,
        
                                                                  $this->user__creation_dt,
                                                                  $this->user__update_dt)
            //
                                                    ->index($this->user__id)
            //
                                                    ->withConstraints(PrimaryKeyConstraintSchema::init()
                                                                                                ->addColumn($this->user__email_address)
                                                                                                ->addColumn($this->user__id),
                                                                      UniqueKeyConstraintSchema::init()
                                                                                               ->addColumn($this->user__firstName, $this->user__email_address));
        
        $result = $this->formatterManager->format($createTableStatement,
                                                  new SqlExecutionContext);
        return $result;
    }
    public function createTable_clients(): string {
        $createTableStatement = CreateTableStatement::init($this->table__clients->getName())
                                                    ->withColumns(
                                                        $this->clients__id,
                                                        $this->clients__note,
                                                        $this->clients__creation_dt,
                                                        $this->clients__update_dt,
                                                        $this->clients__client_id,
                                                        $this->clients__user_id
                                                    )
            //
                                                    ->index($this->clients__id)
                                                    ->index($this->clients__user_id)
                                                    ->index($this->clients__client_id)
            //
                                                    ->withConstraints(PrimaryKeyConstraintSchema::init()
                                                                                                ->addColumn($this->clients__id),
                                                                      UniqueKeyConstraintSchema::init()
                                                                                               ->addColumn($this->clients__client_id,
                                                                                                           $this->clients__user_id));
        
        return $this->formatterManager->format($createTableStatement, new SqlExecutionContext);
    }
}

class MySqlQueryInterpreterTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Modules\Sql\MySql\MySqlQueryInterpreter $interpreter */
    protected $interpreter;
    /** @var  \Sm\Modules\Sql\MySql\_Queries */
    protected $queries;
    /** @var  \Sm\Modules\Sql\MySql\Module\MySqlQueryModuleProxy */
    protected $mysqlQueryModule;
    
    
    public function testCreateTable() {
        $createTables = $this->queries->createTables();
        foreach ($createTables as $createTableStmt) {
            $result = $this->interpreter->interpret($createTableStmt);
            var_dump($result);
        }
    }
    public function testInsert() {
        $result2 = $this->mysqlQueryModule->interpret($this->queries->insert1());
        var_dump($result2);
    }
    public function testUpdate() {
        $result2 = $this->mysqlQueryModule->interpret($this->queries->update1());
        var_dump($result2);
    }
    public function testCanSelect() {
        $result1 = $this->interpreter->interpret("SELECT 'hello' as test;");
        $this->assertInternalType('array', $result1);
        $this->assertEquals('hello', $result1['test']);
        
        $result2 = $this->mysqlQueryModule->interpret($this->queries->select1());
        var_dump($result2);
    }
    
    protected function setUp() {
        $authentication         = MySqlAuthentication::init()
                                                     ->setCredentials("codozsqq",
                                                                      "^bzXfxDc!Dl6",
                                                                      "localhost",
                                                                      "sm_test");
        $this->interpreter      = new MySqlQueryInterpreter($authentication,
                                                            new  SqlQueryFormatterManager(new FormatterFactory,
                                                                                          new SqlFormattingProxyFactory,
                                                                                          new SqlFormattingAliasContainer));
        $this->mysqlQueryModule = MySqlQueryModule::init()->initialize()->registerAuthentication($authentication);
        $formatterManager       = $this->mysqlQueryModule->getQueryFormatter();
        $this->queries          = new _Queries($formatterManager);
    }
    
}
