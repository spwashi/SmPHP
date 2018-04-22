<?php

namespace Sm\Data\Model;


use Sm\Data\Property\PropertySchema;
use Sm\Data\Property\PropertySchemaContainer;
use Sm\Data\Property\ReferenceDescriptorSchematic;
use Sm\Modules\Query\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Query\MySql\MySqlQueryModule;

class TestModel extends Model {
}

class ModelDataManagerTest extends \PHPUnit_Framework_TestCase {
    protected $queryModule;
    public function setUp() {
        $module = new MySqlQueryModule;
        $module->registerAuthentication(MySqlAuthentication::init()
                                                           ->setCredentials("codozsqq",
                                                                            "^bzXfxDc!Dl6",
                                                                            "localhost",
                                                                            'sm_test'));
        
        $this->queryModule = $module;
        
    }
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
    public function testCanInstantiateConfiguredModel() {
        $mdm           = ModelDataManager::init();
        $model_name    = 'modelName';
        $configuration = [ 'name' => $model_name, 'smID' => '[Model]modelName' ];
        $modelSchema   = $mdm->configure($configuration);
        $this->assertInstanceOf(Model::class, $mdm->instantiate('[Model]modelName'));
        $this->assertEquals($model_name, $modelSchema->getName());
    }
    public function testCanRegisterModelClassForSmID() {
        $mdm = ModelDataManager::init();
        $mdm->registerResolver(function ($smID, $schematic) {
            if ($smID === '[Model]modelName') {
                return new TestModel;
            }
            return null;
        });
        $model_name    = 'modelName';
        $configuration = [ 'name' => $model_name, 'smID' => '[Model]modelName' ];
        $modelSchema   = $mdm->configure($configuration);
        $this->assertInstanceOf(TestModel::class, $mdm->instantiate('[Model]modelName'));
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
    /**
     *
     */
    public function testCanConfigurePropertiesAsReferences() {
        $mdm                       = ModelDataManager::init();
        $model_name                = 'id';
        $expected__hydrationMethod = '[Property]{[Model]example}id';
        $configuration             = [
            'name'       => $model_name,
            'properties' => [
                'id' => [
                    'reference' => [
                        'identity'        => '[Model]example',
                        'hydrationMethod' => $expected__hydrationMethod,
                    ],
                ],
            ],
        ];
        
        /** @var \Sm\Data\Model\ModelSchematic $modelSchema */
        $modelSchema             = $mdm->configure($configuration);
        $propertySchemaContainer = $modelSchema->getProperties();
        
        $this->assertInstanceOf(PropertySchemaContainer::class, $propertySchemaContainer);
        
        /** @var \Sm\Data\Property\PropertySchematic $propertySchema */
        $propertySchema = $propertySchemaContainer->id;
        /** @var ReferenceDescriptorSchematic $referenceDescriptor */
        $referenceDescriptor = $propertySchema->getReferenceDescriptor();
        $this->assertInstanceOf(ReferenceDescriptorSchematic::class, $referenceDescriptor);
        
        $hydrationMethod = $referenceDescriptor->getHydrationMethod();
        $this->assertEquals($expected__hydrationMethod, $hydrationMethod);
    }
    public function testCanCreateModels() {
        $mdm           = ModelDataManager::init();
        $configuration = [ 'name' => 'users', 'properties' => [ 'id' => [] ] ];
        $modelSchema   = $mdm->configure($configuration);
        
        $model = new Model;
        $model->fromSchematic($modelSchema);
        $model->set('id', 1);
        
        $persistenceManager = new StandardModelPersistenceManager;
        $mdm->setPersistenceManager($persistenceManager->setQueryInterpreter($this->queryModule));
        $item = $mdm->persistenceManager->find($model);
        var_dump(json_encode($item));
    }
}
