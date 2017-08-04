<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:48 PM
 */

namespace Sm\Data\Source;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;
use Sm\Core\SmEntity\ConfigurableSmEntity;
use Sm\Core\SmEntity\StdSmEntityTrait;
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
abstract class DataSource implements ConfigurableSmEntity,
                                     Identifiable,
                                     DataSourceSchema {
    # Traits
    use HasObjectIdentityTrait;
    use StdSmEntityTrait;
    
    # Properties
    /** @var string The SmID of the prototype of the DataSources */
    protected $protoSmID = 'DataSource';
    
    ####################################
    #   Constructors/Initialization
    ####################################
    public function __construct() { $this->createSelfID(); }
    /**
     * Static constructor
     *
     * @param null $Authentication
     *
     * @return static
     */
    public static function init($Authentication = null) {
        return new static(...func_get_args());
    }
    
    ####################################
    #   Getters/Setters/Configuration
    ####################################
    public function configure($configuration) {
        throw new UnimplementedError("Cannot yet configure DataSources");
    }
    /**
     * Get the root DataSource of this DataSource. Useful for subsources
     *
     * @return \Sm\Data\Source\DataSource
     */
    public function getParentSource() {
        return null;
    }
}