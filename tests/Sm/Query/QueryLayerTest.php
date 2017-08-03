<?php
/**
 * User: Sam Washington
 * Date: 7/23/17
 * Time: 7:21 PM
 */

namespace Sm\Query;

use PHPUnit\Framework\TestCase;
use Sm\Core\Module\ModuleContainer;
use Sm\Query\Module\QueryModuleFactory;
use Sm\Query\Modules\Sql\MySql\Module\MySqlQueryModule;

/**
 * Class QueryLayerTest
 *
 * @package Sm\Query
 */
class QueryLayerTest extends TestCase {
    /** @var  \Sm\Query\QueryLayer $layer */
    public $layer;
    public function setUp() {
        $this->layer = new QueryLayer(new ModuleContainer, new QueryModuleFactory);
    }
    public function testCanRegisterMySqlModule() {
        $this->layer->registerQueryModule(new MySqlQueryModule);
    }
}
