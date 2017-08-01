<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:11 AM
 */

namespace Sm\Core\Resolvable;

/**
 * Class NullResolvableTest
 *
 * @package Sm\Core\Resolvable
 */
class NullResolvableTest extends NativeResolvableTest {
    public function testCanCreate() {
        $Resolvable = new NullResolvable(null);
        $this->assertInstanceOf(NullResolvable::class, $Resolvable);
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
        $Resolvable = new NullResolvable($subject);
        $this->assertTrue(null === $Resolvable->resolve());
    }
}
