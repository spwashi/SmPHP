<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:17 PM
 */

namespace Sm\Modules\Query\Sql\Formatting\Component;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Evaluation\TwoOperandStatement;
use Sm\Data\Property\PropertySchema;
use Sm\Modules\Query\Sql\Data\Column\ColumnSchema;
use Sm\Modules\Query\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\SqlQueryFormatter;

class TwoOperandStatementFormatter extends SqlQueryFormatter {
    public function format($stmt): string {
        if (!($stmt instanceof TwoOperandStatement)) throw new InvalidArgumentException("Can only format TwoOperandStatements");
        $right = $this->proxyRightSide($stmt);
        $left  = $this->proxyLeftSide($stmt);
        return $this->completeFormatting($stmt, $left, $right);
    }
    
    protected function proxyRightSide(TwoOperandStatement $stmt) {
        $right = $stmt->getRightSide();
        
        if (!($right instanceof ColumnSchema)) {
            $right = $this->formatterManager->placeholder($right);
        }
        # Format each side like we're using columns
        if ($right instanceof ColumnSchema) {
            $right = $this->proxy($right, ColumnIdentifierFormattingProxy::class);
        }
        return $right;
    }
    protected function proxyLeftSide(TwoOperandStatement $stmt) {
        $left = $stmt->getLeftSide();
        if ($left instanceof ColumnSchema || $left instanceof PropertySchema) {
            $left = $this->proxy($left, ColumnIdentifierFormattingProxy::class);
        }
        return $left;
    }
    /**
     * @param $stmt
     * @param $left
     * @param $right
     *
     * @return string
     */
    protected function completeFormatting(TwoOperandStatement $stmt, $left, $right): string {
        $formattedLeft  = $this->formatComponent($left);
        $formattedRight = $this->formatComponent($right);
        $operator       = $stmt->getOperator();
        return $formattedLeft . ' ' . $operator . ' ' . $formattedRight;
    }
}