<?php
/**
 * User: Sam Washington
 * Date: 2/19/17
 * Time: 1:54 AM
 */

namespace Sm\Core\Factory;


use Sm\Core\Resolvable\Error\UnresolvableException;

class FactoryContainerTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolveFactory() {
        $FactoryMock = $this->getMockBuilder(StandardFactory::class)
                            ->setMethods([ 'build' ])->getMock();
        $FactoryMock->method('build')->willReturn('test');
        $FactoryContainer = new FactoryContainer;
        $FactoryContainer->register(StandardFactory::class, $FactoryMock);
        $this->assertEquals('test', $FactoryContainer->resolve(StandardFactory::class)->build());
        $this->assertEquals('test', $FactoryContainer->resolve('StandardFactory')->build());
        
        $this->expectException(UnresolvableException::class);
        $FactoryContainer->resolve('DoesnotExistFactory')->build();
    }
}
