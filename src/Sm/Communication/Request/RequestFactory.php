<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 5:41 PM
 */

namespace Sm\Communication\Request;


use Sm\Core\Factory\StandardFactory;

/**
 * Class RequestFactory
 *
 * Factory to build Requests
 *
 * @package Sm\Communication\Request
 */
class RequestFactory extends StandardFactory {
//    protected $do_create_missing = false;
    /**
     * @param null $name
     * @param null $registrant
     *
     * @return $this
     */
    public function register($name = null, $registrant = null) {
        return parent::register($name, $registrant);
    }
    
    /**
     * @param object|string $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        return parent::canCreateClass($object_type);
    }
    
}