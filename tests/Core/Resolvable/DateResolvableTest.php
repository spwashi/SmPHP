<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:29 PM
 */

namespace Sm\Core\Resolvable;


class DateResolvableTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $DateResolvable = new DateResolvable();
        $this->assertInstanceOf(DateResolvable::class, $DateResolvable);
    }
    public function testCanResolve() {
        $DateResolvable = new DateResolvable();
        $this->assertInstanceOf(\DateTime::class, ($DateResolvable->resolve()));
    }
}
