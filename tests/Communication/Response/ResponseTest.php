<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:05 PM
 */

namespace Sm\Communication\Response;


class ResponseTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Response = $this->getMockForAbstractClass(AbstractResponse::class);
        $this->assertInstanceOf(AbstractResponse::class, $Response);
    }
    
}
