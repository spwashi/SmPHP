<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 5:18 PM
 */

namespace Sm\Core\Paths;


use Sm\Core\Resolvable\Error\UnresolvableException;

class PathContainerTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolvePaths() {
        $pathContainer = new PathContainer();
        $pathContainer->register('test', 'dirt');
        $resolve = $pathContainer->resolve('test');
        $this->assertEquals('dirt/', $resolve);
    
        $this->expectException(UnresolvableException::class);
        PathContainer::init()->resolve('nothing');
    }
}
