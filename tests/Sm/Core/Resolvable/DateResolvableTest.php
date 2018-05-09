<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:29 PM
 */

namespace Sm\Core\Resolvable;


class DateResolvableTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $DateResolvable = new DateTimeResolvable();
        $this->assertInstanceOf(DateTimeResolvable::class, $DateResolvable);
    }
    public function testCanResolve() {
        $DateResolvable = new DateTimeResolvable();
        $this->assertInstanceOf(\DateTime::class, ($DateResolvable->resolve()));
    }
}
