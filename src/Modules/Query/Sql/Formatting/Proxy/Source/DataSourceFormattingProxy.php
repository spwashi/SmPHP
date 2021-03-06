<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 7:39 PM
 */

namespace Sm\Modules\Query\Sql\Formatting\Proxy\Source;


use Sm\Core\Formatting\FormattingProxy;
use Sm\Data\Source\Schema\DataSourceSchema;

interface DataSourceFormattingProxy extends FormattingProxy, DataSourceSchema {
    
}