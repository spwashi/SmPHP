<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:11 AM
 */

namespace Sm\Core\Resolvable;

/**
 * Class NativeResolvableTest
 *
 * @package Sm\Core\Resolvable
 */
class NativeResolvableTest extends ResolvableTest {
    public function testCanCreate() {
        $Resolvable = new NativeResolvable(null);
        $this->assertInstanceOf(NativeResolvable::class, $Resolvable);
        return $Resolvable;
    }
    public function genericSubjectProvider() {
        return [
            [ "test" ],
            [ 1 ],
            [ null ],
            [ [] ],
        ];
    }
    /**
     * @dataProvider genericSubjectProvider
     *
     * @param $subject
     */
    public function testCanResolveCorrectly($subject = null) {
        $Resolvable = new NativeResolvable($subject);
        $this->assertTrue($subject === $Resolvable->resolve());
    }
}
