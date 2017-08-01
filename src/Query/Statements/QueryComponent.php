<?php
/**
 * User: Sam Washington
 * Date: 7/10/17
 * Time: 6:56 PM
 */

namespace Sm\Query\Statements;

use Sm\Data\Source\Schema\DataSourceSchemaGarage;

/**
 * Class QueryComponent
 *
 * Something used to execute a Query
 *
 * @package Sm\Query\Statements
 */
class QueryComponent {
    /** @var  \Sm\Data\Source\Schema\DataSourceSchemaGarage */
    private $sourceGarage;
    /**
     * @return static
     */
    public static function init() {
        return new static(...func_get_args());
    }
    /**
     * This is what helps us figure out what the DataSource of whatever we're working with is
     *
     * @return DataSourceSchemaGarage
     */
    public function getSourceGarage(): DataSourceSchemaGarage {
        return $this->sourceGarage = $this->sourceGarage ?? new DataSourceSchemaGarage;
    }
    /**
     * Set the thing that is going to resolve where the Source of the item is located
     *
     * @param \Sm\Data\Source\Schema\DataSourceSchemaGarage $sourceGarage
     *
     * @return QueryComponent
     */
    public function setSourceGarage(DataSourceSchemaGarage $sourceGarage): QueryComponent {
        $this->sourceGarage = $sourceGarage;
        return $this;
    }
}