<?php

namespace Sm\Data\Type;


class DatatypeFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolveStd() {
        $datatypeFactory = DatatypeFactory::init();
        $instance        = $datatypeFactory->resolve('int');
        $this->assertInstanceOf(Integer_::class, $instance);
    }
}
