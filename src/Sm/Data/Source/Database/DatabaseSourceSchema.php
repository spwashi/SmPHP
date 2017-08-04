<?php


namespace Sm\Data\Source\Database;


use Sm\Data\Source\Schema\DataSourceSchema;

interface DatabaseSourceSchema extends DataSourceSchema {
    /**
     * Get the name of the database
     *
     * @return mixed
     */
    public function getName();
}