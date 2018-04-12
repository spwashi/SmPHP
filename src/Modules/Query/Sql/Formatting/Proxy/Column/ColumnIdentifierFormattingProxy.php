<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 7:44 AM
 */

namespace Sm\Modules\Query\Sql\Formatting\Proxy\Column;


use Sm\Data\Source\Schema\DataSourceSchema;
use Sm\Modules\Query\Sql\Formatting\Proxy\SqlFormattingProxy;

/**
 * Class ColumnFormattingProxy
 *
 * For columns as they are used in typical queries
 *
 * @package Sm\Modules\Query\Sql\Formatting\Proxy
 */
abstract class ColumnIdentifierFormattingProxy extends SqlFormattingProxy {
    abstract public function getSource(): ? DataSourceSchema;
    abstract public function getColumnName(): ?string;
}