<?php
/**
 * User: Sam Washinlton
 * Date: 7/12/17
 * Time: 9:15 PM
 */

namespace Sm\Data\Evaluation\Comparison;


class LessThanConditionTest extends \PHPUnit_Framework_TestCase {
    public function testCanCompareDefault() {
        $ltc = new LessThanCondition(2, 1);
        $this->assertFalse($ltc->resolve());
        
        $ltc_2 = new LessThanCondition(1, 2);
        $this->assertTrue($ltc_2->resolve());
        
        $ltc_3 = new LessThanCondition(1, 1);
        $this->assertFalse($ltc_3->resolve());
    }
}
