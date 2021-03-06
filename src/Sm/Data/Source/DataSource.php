<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:48 PM
 */

namespace Sm\Data\Source;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;
use Sm\Core\SmEntity\Traits\Is_StdSmEntityTrait;
use Sm\Data\Source\Schema\DataSourceSchema;

/**
 * Class DataSource
 *
 * Represents something where Data comes from. Vague, I know.
 *
 * Meant to eventually be the backbone of the Query layer
 *
 * @package Sm\Data\Source
 */
abstract class DataSource implements Identifiable,
                                     DataSourceSchema {
    # Traits
    use Is_StdSmEntityTrait;
    
    # Properties
    
    ####################################
    #   Constructors/Initialization
    ####################################
    public function __construct() { $this->createSelfID(); }
    
    ####################################
    #   Getters/Setters/Configuration
    ####################################
    /**
     * Get the root DataSource of this DataSource. Useful for subsources
     *
     * @return \Sm\Data\Source\DataSource
     */
    public function getParentSource() {
        return null;
    }
}