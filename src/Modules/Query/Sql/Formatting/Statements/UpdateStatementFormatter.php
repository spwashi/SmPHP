<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 11:40 PM
 */

namespace Sm\Modules\Query\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Modules\Query\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\Proxy\Source\Table\TableIdentifierFormattingProxy;
use Sm\Modules\Query\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\UpdateStatement;

/**
 * Class UpdateStatementFormatter
 *
 * Formatter for Update Statements
 *
 * @package Sm\Modules\Query\Sql\Formatting\Statements
 */
class UpdateStatementFormatter extends SqlQueryFormatter {
	public function format($item): string {
		if (!($item instanceof UpdateStatement)) throw new InvalidArgumentException("Can only format UpdateStatements");

		$update_expression_list = $this->formatUpdateExpressionList($item->getUpdatedItems());
		$whereClause            = $item->getWhereClause();
		$where_string           = $whereClause ? "WHERE\t" . $this->formatComponent($whereClause) : '';
		$source_string          = $this->formatSourceList($item->getIntoSources());

		$update_stmt = "UPDATE {$source_string} \nSET\t{$update_expression_list}\n{$where_string}";

		$update_stmt = trim($update_stmt);

		return $update_stmt;
	}
	/**
	 * Format the list of sources as they are going to be used in the UPDATE statement
	 *
	 * @param $source_array
	 *
	 * @return string
	 * @throws \Sm\Core\Formatting\Formatter\Exception\IncompleteFormatterException
	 */
	protected function formatSourceList($source_array): string {
		$sources = [];
		foreach ($source_array as $index => $source) {
			$sources[] = $this->formatComponent($this->proxy($source, TableIdentifierFormattingProxy::class));
		}
		return join(', ', $sources);
	}
	protected function formatUpdateExpressionList(array $updates) {
		$expression_list = [];
		foreach ($updates as $item) {
			if (!is_array($item)) {
				throw new UnimplementedError("+ Anything but associative in the expression list");
			}

			foreach ($item as $column_identifier => $new_value) {
				$columnIdentifierProxy = $this->proxy($column_identifier, ColumnIdentifierFormattingProxy::class);
				$equalTo               = EqualToCondition::init($columnIdentifierProxy, $new_value);
				$expression_list[]     = $this->formatComponent($equalTo);
			}
		}
		return join(",\n\t", $expression_list);
	}
}