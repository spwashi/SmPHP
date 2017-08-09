<?php

namespace Sm\Data\Model;


use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchemaContainer;

class ModelDataManagerTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolveDefaultModel() {
        $mdm = ModelDataManager::init();
        $this->assertInstanceOf(Model::class, $mdm->instantiate());
    }
    public function testCanConfigureModel() {
        $mdm           = ModelDataManager::init();
        $model_name    = 'id';
        $configuration = [ 'name' => $model_name ];
        $modelSchema   = $mdm->configure($configuration);
        $this->assertInstanceOf(ModelSchematic::class, $modelSchema);
        $this->assertEquals($model_name, $modelSchema->getName());
    }
    public function testCanConfigureProperties() {
        $mdm                     = ModelDataManager::init();
        $model_name              = 'id';
        $configuration           = [
            'name'       => $model_name,
            'properties' => [
                'id' => [
                    'datatypes' => [ 'int' ],
                ],
            ],
        ];
        $modelSchema             = $mdm->configure($configuration);
        $propertySchemaContainer = $modelSchema->getProperties();
        
        $this->assertInstanceOf(ModelSchematic::class, $modelSchema);
        $this->assertEquals($model_name, $modelSchema->getName());
        
        $this->assertInstanceOf(PropertySchemaContainer::class, $propertySchemaContainer);
        $propertySchema = $propertySchemaContainer->id;
        $this->assertInstanceOf(PropertySchema::class, $propertySchema);
    }
}
