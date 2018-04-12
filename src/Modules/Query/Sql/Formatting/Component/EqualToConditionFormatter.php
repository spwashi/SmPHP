<?php


namespace Sm\Modules\Query\Sql\Formatting\Component;


use Sm\Data\Evaluation\TwoOperandStatement;

class EqualToConditionFormatter extends TwoOperandStatementFormatter {
    protected function completeFormatting(TwoOperandStatement $stmt, $left, $right): string {
        $formattedLeft = $this->formatComponent($left);
        if (!isset($right)) {
            return $formattedLeft . ' IS NULL';
        }
        return parent::completeFormatting($stmt, $left, $right);
    }
    
}