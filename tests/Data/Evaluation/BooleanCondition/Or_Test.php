<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 1:33 PM
 */

namespace Sm\Data\Evaluation\BooleanCondition;


class Or_Test extends \PHPUnit_Framework_TestCase {
    public function testCanResolveStandard() {
        $and = new Or_;
        $this->assertTrue($and->evaluate(true));
        $this->assertTrue($and->evaluate(true, 1, 'hello'));
        $this->assertTrue($and->evaluate(true, 0, 'hello'));
        $this->assertTrue($and->evaluate(true, ''));
        $this->assertFalse($and->evaluate(0, false));
        $this->assertFalse($and->evaluate(false));
    }
}
