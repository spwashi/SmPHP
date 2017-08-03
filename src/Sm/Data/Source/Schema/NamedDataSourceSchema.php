<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 8:00 PM
 */

namespace Sm\Data\Source\Schema;

/**
 * Interface NamedDataSourceSchema
 *
 * @package Sm\Data\Source
 */
interface NamedDataSourceSchema extends DataSourceSchema {
    public function getName():?string;
}