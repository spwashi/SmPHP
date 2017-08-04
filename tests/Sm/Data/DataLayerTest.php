<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:35 AM
 */

namespace Sm\Data;


use Sm\Data\Model\ModelDataManager;
use Sm\Data\Model\ModelSchema;
use Sm\Data\Property\PropertyDataManager;
use Sm\Data\Source\DataSourceDataManager;

class DataLayerTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Data\DataLayer */
    protected $dataLayer;
    public function setUp() {
        $this->dataLayer = new DataLayer;
    }
    public function testHasStdSmEntities() {
        $models     = $this->dataLayer->models;
        $sources    = $this->dataLayer->sources;
        $properties = $this->dataLayer->properties;
        
        $this->assertInstanceOf(PropertyDataManager  ::class, $properties);
        $this->assertInstanceOf(DataSourceDataManager::class, $sources);
        $this->assertInstanceOf(ModelDataManager     ::class, $models);
    }
    public function testCanResolveModel() {
        $this->assertInstanceOf(ModelSchema::class, $this->dataLayer->models->configure([ 'name' => 'model_test' ]));
    }
}
