<?php

namespace Sm\Communication\Network\Http\Request;


class HttpRequestDescriptorTest extends \PHPUnit_Framework_TestCase {
    public function testCanDescribeUrl() {
        $httpRD = new HttpRequestDescriptor('/hello/{name}/how_are.you/{end}');
        $this->assertEquals('/hello/sam/how_are.you/doing',
                            $httpRD->asUrlPath([
                                                   'name' => 'sam',
                                                   'end'  => 'doing',
                                               ]));
    }
}
