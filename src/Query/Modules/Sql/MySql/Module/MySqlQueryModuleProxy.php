<?php
/**
 * User: Sam Washington
 * Date: 7/27/17
 * Time: 7:54 PM
 */

namespace Sm\Query\Modules\Sql\MySql\Module;


use Sm\Core\Module\ModuleProxy;

class MySqlQueryModuleProxy extends ModuleProxy {
    /** @var  \Sm\Query\Modules\Sql\MySql\Module\MySqlQueryModule */
    protected $subject;
    public function getQueryFormatter() {
        return $this->subject->getQueryFormatter($this->context);
    }
}