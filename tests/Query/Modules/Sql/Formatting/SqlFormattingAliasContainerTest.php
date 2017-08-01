<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 5:44 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;

class SqlFormattingAliasContainerTest extends \PHPUnit_Framework_TestCase {
    public function testCanGetFinalAlias() {
        $aliasContainer = new SqlFormattingAliasContainer;
        $aliasContainer->register('test', 'test1');
        $aliasContainer->register('test1', 'test2');
        $aliasContainer->register('test2', 'test3');
        
        $this->assertEquals('test3', $aliasContainer->getFinalAlias('test'));
    }
}
