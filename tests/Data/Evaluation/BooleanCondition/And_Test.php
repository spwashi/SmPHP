<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 1:16 PM
 */

namespace Sm\Data\Evaluation\BooleanCondition;


class And_Test extends \PHPUnit_Framework_TestCase {
    public function testCanResolveStandard() {
        $and = new And_;
        $this->assertTrue($and->evaluate(true));
        $this->assertTrue($and->evaluate(true, 1, 'hello'));
        $this->assertFalse($and->evaluate(true, 0, 'hello'));
        $this->assertFalse($and->evaluate(true, ''));
        $this->assertFalse($and->evaluate(false));
    }
}
