<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:19 AM
 */

namespace Sm\Data\Source;


class DataSourceTest extends \PHPUnit_Framework_TestCase {
    public function testSmID() {
        /** @var \Sm\Data\Source\DataSource $ds */
        $ds = $this->getMockForAbstractClass(DataSource::class);
        $this->assertEquals('DataSource', $ds->getPrototypeSmID());
    }
}
