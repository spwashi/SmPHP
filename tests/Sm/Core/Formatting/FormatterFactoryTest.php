<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 1:53 PM
 */

namespace Sm\Core\Formatting\Formatter;


class FormatterFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCanBuild() {
        $formatterFactory = new FormatterFactory();
    }
    public function testCanCreateFormattersOnTheFly() {
        $formatterFactory = new FormatterFactory;
        $formatter        = $formatterFactory->createFormatter(function ($item) { return strtolower($item); });
        $result           = $formatter->format('HELLO');
        $this->assertEquals('hello', $result);
    }
}
