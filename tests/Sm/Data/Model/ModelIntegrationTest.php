<?php

namespace Sm\Data\Model;


use Sm\Application\Application;

class ModelIntegrationTest extends \PHPUnit_Framework_TestCase {
    public function testCanSelectFromModel() {
        $app = Application::init(SM_TEST_PATH)->boot();
    }
}
