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
class AlterTableStatement extends QueryComponent {
    protected $name;
    protected $columns     = [];
    protected $constraints = [];
    public function __construct($name) {
        $this->name = $name;
    }
    public function withConstraints(...$constraints) {
        $this->constraints = array_merge($this->constraints, $constraints);
        return $this;
    }
    public function getTableName() { return $this->name; }
    public function getConstraints() { return $this->constraints; }
}