<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 5:46 PM
 */

namespace Sm\Modules\Query\Sql\Formatting\Proxy\Source\Table;

use Sm\Modules\Query\Sql\Formatting\Proxy\SqlFormattingProxy;

/**
 * Class TableFormattingProxy
 *
 * Formatting Proxy for Tables
 *
 * @package Sm\Modules\Query\Sql\Formatting\Proxy
 */
abstract class TableIdentifierFormattingProxy extends SqlFormattingProxy implements TableFormattingProxy {
    protected $table_name;
}