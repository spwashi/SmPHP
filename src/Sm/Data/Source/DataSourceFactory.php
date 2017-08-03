<?php
/**
 * User: Sam Washington
 * Date: 7/10/17
 * Time: 6:58 PM
 */

namespace Sm\Data\Source;

use Sm\Data\Source\Schema\DataSourceSchemaFactory;


/**
 * Class DataSourceFactory
 *
 * Factory that creates DataSources
 *
 * @package Sm\Data\Source
 */
class DataSourceFactory extends DataSourceSchemaFactory {
    /**
     * @param object|string $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        return is_a($object_type, DataSource::class);
    }
    
}