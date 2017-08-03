<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:10 PM
 */

namespace Sm\Core\Resolvable;


class StringResolvableTest extends NativeResolvableTest {
    public function testCanCreate() {
        $StringResolvable = new StringResolvable;
        $this->assertInstanceOf(StringResolvable::class, $StringResolvable);
        return $StringResolvable;
    }
    public function testCanResolveCorrectly($subject = null) {
        $StringResolvable = new StringResolvable("This is a thing");
        $this->assertEquals("This is a thing", $StringResolvable->resolve());
    }
}
