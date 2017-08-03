<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 10:04 AM
 */

namespace Sm\Core\Formatting\Formatter;


class PlainStringFormatterTest extends \PHPUnit_Framework_TestCase {
    # todo improve the spec here
    public function testResolvesToStrings() {
        $this->assertInternalType('string', (new PlainStringFormatter)->format("This is a test"));
        $this->assertInternalType('string', (new PlainStringFormatter)->format(1));
        $this->assertInternalType('string', (new PlainStringFormatter)->format(null));
    }
}
