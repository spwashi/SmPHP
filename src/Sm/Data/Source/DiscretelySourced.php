<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 9:42 PM
 */

namespace Sm\Data\Source;

use Sm\Data\Source\Schema\DataSourceSchema;

/**
 * Interface DiscretelySourced
 *
 * Item that has once DataSource
 *
 * @package Sm\Data\Source
 */
interface DiscretelySourced {
    public function getDataSource();
}