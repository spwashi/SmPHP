<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 6:00 PM
 */

namespace Sm\Core\Abstraction;


class ReadonlyTraitTest extends \PHPUnit_Framework_TestCase {
    public function testCanBeReadonly() {
        /** @var \Sm\Core\Abstraction\ReadonlyTrait $mock */
        $mock = $this->getMockForTrait(ReadonlyTrait::class);
        
        # Can markReadonly
        $mock->markReadonly();
        $this->assertTrue($mock->isReadonly());
        
        # Can markNotReadOnly
        $mock->markNotReadonly();
        $this->assertFalse($mock->isReadonly());
        
        # Can setReadonly(true)
        $mock->setReadonly(true);
        $this->assertTrue($mock->isReadonly());
        
        # Can setReadonly(false)
        $mock->setReadonly(false);
        $this->assertFalse($mock->isReadonly());
    }
}
