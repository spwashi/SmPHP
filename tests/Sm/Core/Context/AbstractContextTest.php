<?php
/**
 * User: Sam Washington
 * Date: 6/28/17
 * Time: 11:51 PM
 */

namespace Sm\Core\Context;


class AbstractContextTest extends \PHPUnit_Framework_TestCase {
    public function testObjectIDsAreAllUnique() {
        /** @var \Sm\Core\Context\StandardContext $abstr_1 */
        $abstr_1 = $this->getMockForAbstractClass(StandardContext::class);
        /** @var \Sm\Core\Context\StandardContext $abstr_2 */
        $abstr_2 = $this->getMockForAbstractClass(StandardContext::class);
        /** @var \Sm\Core\Context\StandardContext $abstr_3 */
        $abstr_3 = $this->getMockForAbstractClass(StandardContext::class);
        $this->assertNotEquals($abstr_1->getObjectId(), $abstr_2->getObjectId());
        $this->assertNotEquals($abstr_3->getObjectId(), $abstr_2->getObjectId());
        $this->assertNotEquals($abstr_1->getObjectId(), $abstr_3->getObjectId());
    }
}
