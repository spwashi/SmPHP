<?php

namespace Sm\Data\Model;


use Sm\Data\Property\Property;
use Sm\Modules\Query\MySql\Authentication\MySqlAuthentication;
use Sm\Modules\Query\MySql\MySqlQueryModule;

class StandardModelPersistenceManagerTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Data\Model\StandardModelPersistenceManager $modelPersistenceManager */
    protected $modelPersistenceManager;
    /** @var \Sm\Query\Module\QueryModule $queryModule */
    protected $queryModule;
    /**
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function setUp() {
        $module = new MySqlQueryModule;
        $module->registerAuthentication(MySqlAuthentication::init()
                                                           ->setCredentials("codozsqq",
                                                                            "^bzXfxDc!Dl6",
                                                                            "localhost",
                                                                            'sm_test'));
        $this->queryModule             = $module->initialize();
        $this->modelPersistenceManager = (new StandardModelPersistenceManager)->setQueryInterpreter($module);
    }
    public function testCanGetSourceFromModel() {
        $model = new Model;
        $model->setName('user');
        $tableSource = $this->modelPersistenceManager->getModelSource($model);
        var_dump(json_encode($tableSource));
    }
    /**
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
     * @throws \Sm\Data\Model\Exception\ModelNotFoundException
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     */
    public function testCanSelectModel() {
        $model = new Model;
        $model->setName('users');
        $model->registerProperty('id', new Property);
        $model->registerProperty('first_name', new Property);
        $model->registerProperty('last_name', new Property);
        $model->registerProperty('email', new Property);
        
        
        $model->properties->id = 1;
        $result                = $this->modelPersistenceManager->find($model);
        $this->assertInstanceOf(Model::class, $result);
    }
    /**
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function testCanSaveModel() {
        $model = new Model;
        $model->setName('users');
        $model->registerProperty('id', new Property);
        $model->registerProperty('first_name', new Property);
        $model->registerProperty('last_name', new Property);
        $model->registerProperty('email', new Property);
        
        $model->properties->id         = 1;
        $model->properties->email      = 'samgineer@gmail.com';
        $model->properties->first_name = 'Samuel';
        $model->properties->last_name  = 'Washington';
        
        $result = $this->modelPersistenceManager->save($model);
        var_dump(json_encode($this->queryModule->getMonitorContainer()));
    }
    /**
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
     * @throws \Sm\Data\Model\Exception\ModelNotFoundException
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     */
    public function testCanJoinModel() {
        $user = new Model;
        $user->setName('users');
        
        $user->registerProperty('id');
        $user->registerProperty('first_name');
        $user->registerProperty('last_name');
        $user->registerProperty('email');
        
        $user->set('id', 1);
        
        $result = $this->modelPersistenceManager->find($user);
        var_dump(json_encode($result, JSON_PRETTY_PRINT));
        $this->assertInstanceOf(Model::class, $result);
    }
    /**
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     */
    public function testCanCreateModel() {
        $model = new Model;
        $model->setName('users');
        $model->registerProperty('id', new Property);
        $model->registerProperty('first_name', new Property);
        $model->registerProperty('last_name', new Property);
        $model->registerProperty('email', new Property);
        $model->registerProperty('delete_dt', new Property);
        $model->set([
                        'email'      => 'boonmans@spwashi.com',
                        'first_name' => 'Bread',
                        'last_name'  => 'HootSuite',
                    ]);
        $result = $this->modelPersistenceManager->create($model);
        var_dump(json_encode($result, JSON_PRETTY_PRINT));
    }
    /**
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function testCanDestroyModel() {
        $model = new Model;
        $model->setName('users');
        $model->registerProperty('id', new Property);
        $model->registerProperty('delete_dt', new Property);
        
        $model->properties->id = 4;
        
        $result = $this->modelPersistenceManager->delete($model);
        var_dump($result);
    }
}