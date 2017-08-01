<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 10:12 PM
 */

namespace Sm\Data\Source\Database\Table;


use Sm\Core\Internal\Identification\Identifiable;
use Sm\Data\Source\Schema\DataSourceSchema;
use Sm\Data\Source\Schema\NamedDataSourceSchema;

interface TableSourceSchema extends NamedDataSourceSchema, Identifiable {
    public function getName():?string;
}