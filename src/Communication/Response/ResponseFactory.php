<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 5:41 PM
 */

namespace Sm\Communication\Response;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Factory\StandardFactory;

/**
 * Class ResponseFactory
 *
 * Factory to build Responses
 *
 * @package Sm\Communication\Response
 */
class ResponseFactory extends StandardFactory {
    protected $do_create_missing = false;
    
    /**
     * @param object|string $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        throw new UnimplementedError("Copy Request");
        return parent::canCreateClass($object_type);
    }
    
}