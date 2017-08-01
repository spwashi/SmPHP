<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:11 AM
 */

namespace Sm\Core\Resolvable;


/**
 * Class OnceCalledResolvableTest
 *
 * @package Sm\Core\Resolvable
 */
class OnceRunResolvableTest extends FunctionResolvableTest {
    public function genericSubjectProvider() {
        return [
            [ "test" ],
            [ 1 ],
            [ null ],
            [ [] ],
        ];
    }
    public function testCanCreate() {
        $Resolvable = new OnceRunResolvable(function () { });
        $this->assertInstanceOf(OnceRunResolvable::class, $Resolvable);
        return $Resolvable;
    }
    /**
     * @dataProvider genericSubjectProvider
     *
     * @param $subject
     */
    public function testCanResolveCorrectly($subject) {
        $Resolvable = new OnceRunResolvable(function () use ($subject) { return $subject; });
        $this->assertTrue($subject === $Resolvable->resolve());
    }
    public function testOnlyResolvesOnce() {
        $Resolvable = new OnceRunResolvable(function ($subject) { return $subject; });
        $Resolvable->resolve("one");
        $result = $Resolvable->resolve("two");
        $this->assertEquals("one", $result);
    }
}
