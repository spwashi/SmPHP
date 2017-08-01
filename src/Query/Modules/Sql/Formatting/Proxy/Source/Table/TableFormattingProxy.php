<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 8:48 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Source\Table;

use Sm\Core\Formatting\FormattingProxy;
use Sm\Data\Source\Database\Table\TableSourceSchema;


/**
 * Class TableFormattingProxy
 *
 * Formatting Proxy for Tables
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy
 */
interface TableFormattingProxy extends TableSourceSchema, FormattingProxy {
}