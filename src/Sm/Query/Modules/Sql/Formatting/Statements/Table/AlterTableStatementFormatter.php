<?php
/**
 * User: Sam Washington
 * Date: 7/26/17
 * Time: 8:14 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements\Table;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Constraints\KeyConstraintSchema;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Modules\Sql\Statements\AlterTableStatement;

class AlterTableStatementFormatter extends SqlQueryFormatter {
    
    public function format($item): string {
        if (!($item instanceof AlterTableStatement)) throw new UnimplementedError("+ Anything but AlterTableStatements");
        $table_name          = $item->getTableName();
        $constraints         = $item->getConstraints();
        $formattedConstraint = null;
        
        
        foreach ($constraints as $constraint) {
            if (!($constraint instanceof KeyConstraintSchema)) throw new InvalidArgumentException("Can only create tables with KeyConstraints");
            if ($formattedConstraint) throw new UnimplementedError("Cannot perform more than one alteration at a time");
            $formattedConstraint = $this->formatComponent($constraint);
        }
        
        
        return "ALTER TABLE {$table_name}\nADD $formattedConstraint";
    }
    
}