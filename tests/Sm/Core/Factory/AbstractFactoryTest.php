<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 8:44 AM
 */

namespace Sm\Core\Factory;


use Sm\Core\Factory\Exception\WrongFactoryException;

class FactoryStub extends StandardFactory {
    public function canCreateClass($classname) {
        return false;
    }
}

class AbstractFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCannotRegisterUnlessAllowedTo() {
        /** @var \Sm\Core\Factory\StandardFactory $mockFactory */
        $mockFactory = new FactoryStub;
        $mockFactory->setCreationMode(StandardFactory::MODE_DO_CREATE_MISSING);
        $this->expectException(WrongFactoryException::class);
        $registrand       = new \stdClass;
        $registrand->name = 'test';
        $mockFactory->register(\stdClass::class, $registrand);
        $result = $mockFactory->resolve(\stdClass::class);
    }
}
