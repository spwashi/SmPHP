<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 7:39 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;

const DO_ECHO_RESULTS = 0;

use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Source\Constructs\JoinedSourceSchematic;
use Sm\Data\Source\Database\DatabaseDataSource;
use Sm\Data\Source\Database\Table\TableSource;
use Sm\Data\Source\Database\Table\TableSourceSchematic;
use Sm\Query\Modules\Sql\Constraints\ForeignKeyConstraintSchema;
use Sm\Query\Modules\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Query\Modules\Sql\Data\Column\IntegerColumnSchema;
use Sm\Query\Modules\Sql\Data\Column\VarcharColumnSchema;
use Sm\Query\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Query\Modules\Sql\MySql\Module\MySqlQueryModule;
use Sm\Query\Modules\Sql\SqlExecutionContext;
use Sm\Query\Modules\Sql\Statements\AlterTableStatement;
use Sm\Query\Modules\Sql\Statements\CreateTableStatement;
use Sm\Query\Statements\InsertStatement;
use Sm\Query\Statements\SelectStatement;
use Sm\Query\Statements\UpdateStatement;

class SqlQueryFormatterTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatterFactory $queryFormatter */
    public $queryFormatter;
    public function setUp() {
        $module               = MySqlQueryModule::init()->initialize();
        $this->queryFormatter = $module->getQueryFormatter();
    }
    
    
    public function testSelect() {
        $tableSource   = new TableSource('tablename_is_here', new DatabaseDataSource('Database'));
        $tableSource_2 = new TableSource('another_table', new DatabaseDataSource('Database'));
        $boonman       = VarcharColumnSchema::init('boonman')
                                            ->setLength(25)
                                            ->setTableSchema($tableSource);
        $bran_slam     = VarcharColumnSchema::init('bran_slam')
                                            ->setLength(25)
                                            ->setTableSchema($tableSource);
        $stmt          = SelectStatement::init('here.column_1', $boonman, $bran_slam, 'column_2')
                                        ->from('there', JoinedSourceSchematic::init()
                                                                             ->setOriginSources($tableSource)
                                                                             ->setJoinConditions(EqualToCondition::init(1, 2))
                                                                             ->setJoinedSources($tableSource_2))
                                        ->where(EqualToCondition::init(1, $bran_slam));
        $result        = $this->queryFormatter->format($stmt, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
    }
    public function testUpdate() {
        $tableSource = new TableSource('tablename_is_here', new DatabaseDataSource('Database'));
        $boonman     = VarcharColumnSchema::init('boonman')
                                          ->setLength(25)
                                          ->setTableSchema($tableSource);
        $stmt        = UpdateStatement::init([ 'test1' => $boonman, 'test4' => 'test5', 'test7' => 13.5 ])
                                      ->inSources('testHELP');
        $result      = $this->queryFormatter->format($stmt, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
    }
    public function testInsert() {
        $stmt   = InsertStatement::init()
                                 ->set([ 'title' => 'hello', 'first_name' => 'last_name' ],
                                       [ 'title' => 'hey there', 'first_name' => 'another' ])
                                 ->inSources('tbl');
        $result = $this->queryFormatter->format($stmt, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
    }
    public function testCreateTable() {
        $vcColumn1 = VarcharColumnSchema::init('column_name')
                                        ->setNullability(0)
                                        ->setLength(255);
        $iColumn1  = IntegerColumnSchema::init('one_thing')
                                        ->setAutoIncrement()
                                        ->setLength(10);
        $vcColumn2 = VarcharColumnSchema::init()
                                        ->setLength(255)
                                        ->setName('this_thing');
        $vcColumn3 = VarcharColumnSchema::init('boon_man')
                                        ->setLength(255);
        
        $primaryKey = PrimaryKeyConstraintSchema::init()
                                                ->addColumn($vcColumn1)
                                                ->addColumn($iColumn1);
        $stmt       = CreateTableStatement::init('TableName')
                                          ->withColumns($vcColumn1,
                                                        $iColumn1,
                                                        $vcColumn2,
                                                        $vcColumn3)
                                          ->index($iColumn1)
                                          ->withConstraints($primaryKey);
        
        $result = $this->queryFormatter->format($stmt, new SqlExecutionContext);
        
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
    }
    public function testAlterTable() {
        $iColumn1 = IntegerColumnSchema::init('one_thing')
                                       ->setAutoIncrement()
                                       ->setLength(10);
        $otherr   = IntegerColumnSchema::init('another_one_thing')
                                       ->setLength(10)
                                       ->setTableSchema(TableSourceSchematic::init('other_tablename'));
        
        $stmt   = AlterTableStatement::init('TableName')
                                     ->withConstraints(ForeignKeyConstraintSchema::init()
                                                                                 ->setConstraintName('table_table_id')
                                                                                 ->addColumn($iColumn1)
                                                                                 ->addRefeferencedColumns($otherr));
        $result = $this->queryFormatter->format($stmt, new SqlExecutionContext);
        if (DO_ECHO_RESULTS) echo __FILE__ . "\n--\n$result\n\n";
    }
}
