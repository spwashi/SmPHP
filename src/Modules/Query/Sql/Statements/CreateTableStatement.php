<?php
/**
 * User: Sam Washington
 * Date: 7/18/17
 * Time: 11:34 AM
 */

namespace Sm\Modules\Query\Sql\Statements;


use Sm\Modules\Query\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Query\Statements\QueryComponent;

/**
 * Class CreateTableStatement
 *
 * QueryComponent to Create a Table
 *
 * @package Sm\Modules\Query\Sql\Statements
 */
class CreateTableStatement extends QueryComponent implements \JsonSerializable {
    protected $name;
    protected $columns         = [];
    protected $constraints     = [];
    protected $indexed_columns = [];
    public function __construct($name, ...$columns) {
        $this->name = $name;
        $this->withColumns(...$columns);
    }
    public static function init($name = null, ...$columns): CreateTableStatement {
        if (!isset($name)) throw new \InvalidArgumentException("Must have a name");
        /** @var CreateTableStatement $stmt */
        $stmt = parent::init(...func_get_args());
        return $stmt;
    }
    
    public function withName(string $name) {
        $this->name = $name;
        return $this;
    }
    public function withColumns(...$columns): CreateTableStatement {
        $this->columns = array_merge($this->columns, $columns);
        return $this;
    }
    public function withConstraints(...$constraints) {
        $this->constraints = array_merge($this->constraints, $constraints);
        return $this;
    }
    public function index(...$columns) {
        $this->indexed_columns = $columns;
        return $this;
    }
    public function getColumns(): array { return $this->columns; }
    public function getName() { return $this->name; }
    public function getConstraints() { return $this->constraints; }
    public function getIndexedColumns(): array { return $this->indexed_columns; }
    public function jsonSerialize() {
        $essence             = get_object_vars($this);
        $new_constraints_arr = [];
        if (isset($essence['constraints'])) {
            $_constraints = $essence['constraints'];
            foreach ($_constraints as $key => $value) {
                if ($value instanceof PrimaryKeyConstraintSchema) {
                    unset($_constraints[ $key ]);
                    $new_constraints_arr['primary'] = $value->getColumns();
                }
            }
        }
        $essence['constraints'] = $new_constraints_arr;
        return $essence;
    }
}