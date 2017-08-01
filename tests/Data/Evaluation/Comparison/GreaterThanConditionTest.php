<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 9:10 PM
 */

namespace Sm\Data\Evaluation\Comparison;


class GreaterThanConditionTest extends \PHPUnit_Framework_TestCase {
    public function testCanCompareDefault() {
        $gtc = new GreaterThanCondition(2, 1);
        $this->assertTrue($gtc->resolve());
        
        $gtc_2 = new GreaterThanCondition(1, 2);
        $this->assertFalse($gtc_2->resolve());
        
        $gtc_3 = new GreaterThanCondition(1, 1);
        $this->assertFalse($gtc_2->resolve());
    }
}
