<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 7:48 PM
 */

namespace Sm\Data\Source\Constructs;

use Sm\Data\Source\Schema\DataSourceSchema;

/**
 * Interface JoinedSourceSchema
 *
 * Interface
 *
 * @package Sm\Data\Source\Constructs
 */
interface JoinedSourceSchema extends DataSourceSchema {
    public function getOriginSources(): ?array;
    public function getJoinedSources(): ?array;
    public function getJoinConditions(): ?array;
}