<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:46 AM
 */

namespace Sm\Data\Model;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\SmEntity\ConfigurableSmEntity;
use Sm\Core\SmEntity\StdSmEntityTrait;

/**
 * Class Model
 *
 * Really a DAO (Data Access Object) but named Model because of other MVC Frameworks
 *
 * Models represent a collection of Data, wherever they are, however they are stored.
 * Meant to abstract the basic operations that we will perform on Data, regardless
 * of if they are JSON, a row in a Table (most common) or some other form of Data.
 *
 * Each Model should have a DataSource.
 *
 *
 * @package Sm\Data\Model
 */
class Model implements ConfigurableSmEntity {
    # traits
    use StdSmEntityTrait;
    
    # properties
    /** @var string The SmID of the prototype of the Model */
    static $protoSmID = 'Model';
    /** @var  \Sm\Data\Source\DataSource $dataSource Where the information/identity of the Model will come from */
    protected $dataSource;
    
    # Configuration
    public function configure($configuration) {
        throw new UnimplementedError("Cannot configure Models yet");
    }
}