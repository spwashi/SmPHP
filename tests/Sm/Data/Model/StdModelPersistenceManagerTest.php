<?php

namespace Sm\Data\Model;


use Sm\Data\Property\Property;
use Sm\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Sql\MySql\Module\MySqlQueryModule;

class StdModelPersistenceManagerTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Data\Model\StdModelPersistenceManager $modelPersistenceManager */
    protected $modelPersistenceManager;
    public function setUp() {
        $module = new MySqlQueryModule;
        $module->registerAuthentication(MySqlAuthentication::init()
                                                           ->setCredentials("codozsqq",
                                                                            "^bzXfxDc!Dl6",
                                                                            "localhost",
                                                                            'sm_test'));
        $this->queryModule             = $module->initialize();
        $this->modelPersistenceManager = (new StdModelPersistenceManager)->setQueryInterpreter($module);
    }
    public function testCanSelectModel() {
        $model = new Model;
        $model->setName('users');
        $model->properties->register('id', new Property);
        $model->properties->register('first_name', new Property);
        $model->properties->register('last_name', new Property);
        $model->properties->register('email', new Property);
        
        
        $model->properties->id = 1;
        
        $result = $this->modelPersistenceManager->find($model);
        var_dump($result);
    }
    public function testCanSaveModel() {
        $model = new Model;
        $model->setName('users');
        $model->properties->register('id', new Property);
        $model->properties->register('first_name', new Property);
        $model->properties->register('last_name', new Property);
        $model->properties->register('email', new Property);
        
        $model->properties->id         = 1;
        $model->properties->email      = 'samgineer@gmail.com';
        $model->properties->first_name = 'Samuel';
        $model->properties->last_name  = 'Washington';
        
        $result = $this->modelPersistenceManager->save($model);
        var_dump($result);
    }
    public function testCanCreateModel() {
        $model = new Model;
        $model->setName('users');
        $model->properties->register('id', new Property);
        $model->properties->register('first_name', new Property);
        $model->properties->register('last_name', new Property);
        $model->properties->register('email', new Property);
        $model->properties->register('delete_dt', new Property);
        
        $model->properties->email      = 'sam@spwashi.com';
        $model->properties->first_name = 'Bread';
        $model->properties->last_name  = 'Hootenanny';
        
        $result = $this->modelPersistenceManager->create($model);
        var_dump($result);
    }
    public function testCanDestroyModel() {
        $model = new Model;
        $model->setName('users');
        $model->properties->register('id', new Property);
        $model->properties->register('delete_dt', new Property);
    
        $model->properties->id = 4;
    
        $result = $this->modelPersistenceManager->delete($model);
        var_dump($result);
    }
}
