<?php
/**
 * User: Sam Washington
 * Date: 7/19/17
 * Time: 9:33 PM
 */

namespace Sm\Query\Modules\Sql\Constraints;


use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;

class ForeignKeyConstraintSchema extends StandardKeyConstraintSchema {
    protected $referenced_columns = [];
    protected $constraint_name;
    
    public function __construct(ColumnSchema ...$columns) {
        $this->columns = $columns;
    }
    /**
     * Add the columns being referenced in the ForeignKeyConstraint
     *
     * @param \Sm\Query\Modules\Sql\Data\Column\ColumnSchema[] ...$columns
     *
     * @return $this
     */
    public function addRefeferencedColumns(ColumnSchema ... $columns) {
        $this->referenced_columns = array_merge($this->referenced_columns, $columns);
        return $this;
    }
    /**
     * Get the columns being referenced in this foreign key constraint
     *
     * @return ColumnSchema[]
     */
    public function getReferencedColumns(): array {
        return $this->referenced_columns;
    }
    public function getConstraintName() {
        return $this->constraint_name;
    }
    public function setConstraintName($constraint_name) {
        $this->constraint_name = $constraint_name;
        return $this;
    }
}