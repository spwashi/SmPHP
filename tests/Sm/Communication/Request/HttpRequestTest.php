<?php
/**
 * User: Sam Washington
 * Date: 1/29/17
 * Time: 10:56 PM
 */

namespace Sm\Communication\Request;


use Sm\Communication\Network\Http\Request\HttpRequest;
use Sm\Communication\Network\Http\Request\HttpRequestDescriptor;

class HttpRequestTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Request = HttpRequest::init();
        $this->assertInstanceOf(HttpRequest::class, $Request);
        return $Request;
    }
    
    /**
     * @param HttpRequest $Request
     *
     * @depends testCanCreate
     */
    public function testCanSetUrl($Request) {
        $Request->setUrl('http://spwashi.com');
        $this->assertEquals('http://spwashi.com', $Request->getUrl());
    }
    
    public function testCanCompareUrlToDescriptor() {
        $descriptor = new HttpRequestDescriptor;
        $descriptor->setMatchingUrlPattern('boonman');
        $descriptor->compare(HttpRequest::init('boonman'));
    }
    
    public function testCanGetPathCorrectly() {
        $Request = HttpRequest::init();
        
        $Request->setUrl('http://spwashi.com/this/is/a/thing');
        $this->assertEquals('this/is/a/thing', $Request->getUrlPath());
        
        $Request->setUrl('//spwashi.com/this/is/a/thing');
        $this->assertEquals('this/is/a/thing', $Request->getUrlPath());
    
        $Request->setUrl('one/two/three/four');
        $this->assertEquals('one/two/three/four', $Request->getUrlPath());
        
    }
}
