<?php /** @noinspection PhpUnhandledExceptionInspection */

/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 8:37 PM
 */

namespace Sm\Modules\Query\MySql;


use Sm\Core\Formatting\Formatter\FormatterFactory;
use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Source\Constructs\JoinedSourceSchematic;
use Sm\Data\Source\Database\DatabaseSource;
use Sm\Data\Source\Database\Table\TableSource;
use Sm\Modules\Query\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Query\MySql\Interpretation\MySqlQueryInterpreter;
use Sm\Modules\Query\Sql\Constraints\ForeignKeyConstraintSchema;
use Sm\Modules\Query\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Modules\Query\Sql\Constraints\UniqueKeyConstraintSchema;
use Sm\Modules\Query\Sql\Data\Column\DateTimeColumnSchema;
use Sm\Modules\Query\Sql\Data\Column\IntegerColumnSchema;
use Sm\Modules\Query\Sql\Data\Column\VarcharColumnSchema;
use Sm\Modules\Query\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Modules\Query\Sql\Formatting\SqlFormattingProxyFactory;
use Sm\Modules\Query\Sql\Formatting\SqlQueryFormatterManager;
use Sm\Modules\Query\Sql\SqlExecutionContext;
use Sm\Modules\Query\Sql\Statements\AlterTableStatement;
use Sm\Modules\Query\Sql\Statements\CreateTableStatement;
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
    protected $user__role_id;
    
    /** @var  DateTimeColumnSchema */
    protected $user__creation_dt;
    /** @var  TableSource $table__users */
    protected $table__users;
    /** @var  TableSource $table__clients */
    protected $table__clients;
    protected $clients__creation_dt;
    protected $clients__update_dt;
    protected $clients__client_id;
    protected $clients__user_id;
    protected $clients__id;
    protected $clients__note;
    /** @var TableSource $table__user_roles */
    protected $table__user_roles;
    protected $user_roles__id;
    protected $user_roles__name;
    
    /** @var  \Sm\Modules\Query\Sql\Formatting\SqlQueryFormatterManager $formatterManager */
    private $formatterManager;
    
    #
    ##
    #
    
    public function __construct(SqlQueryFormatterManager $formatterManager) {
        $this->formatterManager = $formatterManager;
        $databaseSource         = new DatabaseSource('Database');
        $this->buildUserRolesTable($databaseSource);
        $this->buildUserTable($databaseSource);
        $this->buildClientsTable($databaseSource);
        
    }
    
    public function select1() {
        $statement = SelectStatement::init(/*'user.' . $this->user__email_address->getName(),*/
            $this->user__id,
            $this->user__firstName,
            $this->user__lastName,
            $this->user__email_address->getName())
                                    ->from(JoinedSourceSchematic::init()
                                                                ->setOriginSources($this->table__users)
                                                                ->setJoinConditions(EqualToCondition::init($this->user__id, $this->clients__user_id))
                                                                ->setJoinedSources($this->table__clients));#->where(EqualToCondition::init($this->user__id, 1))
        
        $result = $this->formatterManager->format($statement, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
        return $statement;
    }
    public function update1() {
        $updateStatement = UpdateStatement::init([
                                                     $this->user__firstName->getName() => 'FIRST_NAME',
                                                     $this->user__lastName->getName()  => 'LAST_NAME',
                                                 ])
                                          ->inSources($this->table__users)
                                          ->where(EqualToCondition::init($this->user__id, 2));
        $result          = $this->formatterManager->format($updateStatement, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
        
        return $updateStatement;
    }
    /**
     * @return \Sm\Query\Statements\InsertStatement
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function insertUsers() {
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
        $insertStatement = InsertStatement::init()->set($bran, $moonshine, $harold, $sam)
                                          ->inSources($this->table__users);
        $result          = $this->formatterManager->format($insertStatement, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
        return $insertStatement;
    }
    public function insertUserRoles() {
        $insertStatement = InsertStatement::init()
                                          ->set([ 'name' => 'Visitor' ], [ 'name' => 'Admin' ])
                                          ->inSources($this->table__user_roles);
        $result          = $this->formatterManager->format($insertStatement, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
        return $insertStatement;
    }
    public function makeCreateTableStatements(): array {
        $result = [ static::createTable_user_roles(), static::createTable_users(), static::createTable_clients() ];
        
        if (DO_ECHO_RESULTS) {
            echo __FILE__;
            foreach ($result as $item) {
                echo "\n--\n$item\n\n";
            }
        }
        
        return $result;
    }
    public function makeDropTableStatements(): array {
        $tables = [ $this->table__clients, $this->table__users, $this->table__user_roles ];
        $result = [];
        foreach ($tables as $table) {
            $result[] = 'DROP TABLE ' . $table->getName();
        }
        
        if (DO_ECHO_RESULTS) {
            echo __FILE__;
            foreach ($result as $item) {
                echo "\n--\n$item\n\n";
            }
        }
        
        return $result;
    }
    public function alterTable_users() {
        $column     = $this->user__role_id;
        $referenced = $this->user_roles__id;
        
        
        $fk_constraint_name   = $this->table__users->getName() . '_' . $this->table__user_roles->getName();
        $foreignKeyConstraint = ForeignKeyConstraintSchema::init()
                                                          ->setConstraintName($fk_constraint_name)
                                                          ->addColumn($column)
                                                          ->addRefeferencedColumns($referenced);
        $alterTableStatement  = AlterTableStatement::init($this->table__users->getName())
                                                   ->withConstraints($foreignKeyConstraint);
        $result               = $this->formatterManager->format($alterTableStatement, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
        return $result;
    }
    public function createTable_user_roles(): string {
        $createTableStatement = CreateTableStatement::init($this->table__user_roles->getName())
                                                    ->withColumns($this->user_roles__id,
                                                                  $this->user_roles__name)
                                                    ->index($this->user__id)
                                                    ->withConstraints(PrimaryKeyConstraintSchema::init()
                                                                                                ->addColumn($this->user_roles__id),
                                                                      UniqueKeyConstraintSchema::init()
                                                                                               ->addColumn($this->user_roles__name));
        
        $result = $this->formatterManager->format($createTableStatement,
                                                  new SqlExecutionContext);
        return $result;
    }
    public function createTable_users(): string {
        # todo
        #        ->setTableSchema(TableSourceSchematic::init('other_tablename'));
        
        $createTableStatement = CreateTableStatement::init($this->table__users->getName())
                                                    ->withColumns($this->user__id,
        
                                                                  $this->user__role_id,
        
                                                                  $this->user__firstName,
                                                                  $this->user__lastName,
        
                                                                  $this->user__email_address,
        
                                                                  $this->user__creation_dt,
                                                                  $this->user__update_dt)
            //
                                                    ->index($this->user__id, $this->user__role_id)
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
    
    protected function buildUserRolesTable($databaseSource): void {
        $this->table__user_roles = new TableSource('user_roles', $databaseSource);
        #
        ##  COLUMNS (USER)
        #
        $this->user_roles__id   = IntegerColumnSchema::init('id')
                                                     ->setAutoIncrement()
                                                     ->setLength(10)
                                                     ->setTableSchema($this->table__user_roles);
        $this->user_roles__name = VarcharColumnSchema::init('name')
                                                     ->setNullability(0)
                                                     ->setLength(40)
                                                     ->setTableSchema($this->table__user_roles);
    }
    protected function buildUserTable($databaseSource): void {
#
        $this->table__users = new TableSource('users', $databaseSource);
        #
        ##  COLUMNS (USER)
        #
        $this->user__id            = IntegerColumnSchema::init('id')
                                                        ->setAutoIncrement()
                                                        ->setLength(10)
                                                        ->setTableSchema($this->table__users);
        $this->user__role_id       = IntegerColumnSchema::init('role_id')
                                                        ->setDefault(1)
                                                        ->setLength(10)
                                                        ->setTableSchema($this->table__users);
        $this->user__email_address = VarcharColumnSchema::init('email_address')
                                                        ->setNullability(0)
                                                        ->setLength(255)
                                                        ->setTableSchema($this->table__users);
        $this->user__firstName     = VarcharColumnSchema::init('first_name')
                                                        ->setLength(255)
                                                        ->setTableSchema($this->table__users);
        $this->user__lastName      = VarcharColumnSchema::init()
                                                        ->setLength(255)
                                                        ->setName('last_name')
                                                        ->setTableSchema($this->table__users);
        $this->user__update_dt     = DateTimeColumnSchema::init('update_dt')
                                                         ->setOnUpdate(DateTimeColumnSchema::CURRENT_TIMESTAMP)
                                                         ->setDefault(null)
                                                         ->setTableSchema($this->table__users);
        $this->user__creation_dt   = DateTimeColumnSchema::init('creation_dt')
                                                         ->setDefault(DateTimeColumnSchema::CURRENT_TIMESTAMP)
                                                         ->setTableSchema($this->table__users);
    }
    protected function buildClientsTable($databaseSource): void {
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
}

class MySqlQueryInterpreterTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Modules\Query\MySql\Interpretation\MySqlQueryInterpreter $interpreter */
    protected $interpreter;
    /** @var  \Sm\Modules\Query\MySql\_Queries */
    protected $QUERIES;
    /** @var  \Sm\Modules\Query\MySql\Proxy\MySqlQueryModuleProxy */
    protected $mysqlQueryModule;
    
    
    public function testStringDropTables() {
        $statements = $this->QUERIES->makeDropTableStatements();
        foreach ($statements as $dropTableStatement) {
            $result = $this->interpreter->interpret($dropTableStatement);
            var_dump($result);
        }
    }
    public function testCreateTable() {
        $createTables = $this->QUERIES->makeCreateTableStatements();
        foreach ($createTables as $createTableStatement) {
            $result = $this->interpreter->interpret($createTableStatement);
            var_dump($result);
        }
        $alterTableStatement = $this->QUERIES->alterTable_users();
        $result              = $this->interpreter->interpret($alterTableStatement);
        var_dump($result);
    }
    public function testInsert() {
        $insertUserRoles = $this->mysqlQueryModule->interpret($this->QUERIES->insertUserRoles());
        var_dump($insertUserRoles);
        $insertUsers = $this->mysqlQueryModule->interpret($this->QUERIES->insertUsers());
        var_dump($insertUsers);
    }
    public function testUpdate() {
        $result2 = $this->mysqlQueryModule->interpret($this->QUERIES->update1());
        var_dump($result2);
    }
    public function testCanSelect() {
        $result1 = $this->interpreter->interpret("SELECT 'hello' as test;");
        $this->assertInternalType('array', $result1);
        $this->assertEquals('hello', $result1['test']);
        
        $result2 = $this->mysqlQueryModule->interpret($this->QUERIES->select1());
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
        $this->QUERIES          = new _Queries($formatterManager);
    }
    
}
