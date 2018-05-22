<?php

namespace Sm\Data\Model;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Property\PropertyDataManager;

class ModelContainerTest extends \PHPUnit_Framework_TestCase {
    public function testThrowsExceptionIfAttemptingInvalidRegistration() {
        $this->expectException(InvalidArgumentException::class);
        (new ModelContainer())->register('model_one', 'here');
    }
    public function testCanRegisterModel() {
        $propertyDataManager = new PropertyDataManager;
        $model_two           = new Model($propertyDataManager);
        (new ModelContainer())->register('model_two', $model_two);
    }
    /**
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     */
    public function testCanSpecifyAllowedModelSmIDs() {
        $propertyDataManager = new PropertyDataManager;
        $schematic           = ModelSchematic::init($propertyDataManager)
                                             ->load([ 'smID' => '[Model]test_three' ]);
        
        $model = new Model($propertyDataManager);
        $model->fromSchematic($schematic);
        
        var_dump($model->getSmID());
        $modelContainer = new ModelContainer;
        
        $modelContainer->expectSmID('[Model]test_three');
    }
}
