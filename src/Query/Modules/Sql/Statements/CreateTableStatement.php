<?php
/**
 * User: Sam Washington
 * Date: 7/18/17
 * Time: 11:34 AM
 */

namespace Sm\Query\Modules\Sql\Statements;


use Sm\Query\Statements\QueryComponent;

/**
 * Class CreateTableStatement
 *
 * QueryComponent to Create a Table
 *
 * @package Sm\Query\Modules\Sql\Statements
 */
class CreateTableStatement extends QueryComponent {
    protected $name;
    protected $columns         = [];
    protected $constraints     = [];
    protected $indexed_columns = [];
    public function __construct($name, ...$columns) {
        $this->name = $name;
        $this->withColumns(...$columns);
    }
    public function withName(string $name) {
        $this->name = $name;
        return $this;
    }
    public function withColumns(...$columns) {
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
}