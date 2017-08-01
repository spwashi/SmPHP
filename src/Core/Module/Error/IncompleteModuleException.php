<?php
/**
 * User: Sam Washington
 * Date: 7/24/17
 * Time: 11:49 AM
 */

namespace Sm\Core\Module\Error;


use Sm\Core\Exception\DevelopmentError;

/**
 * Class IncompleteModuleException
 *
 * When we are trying to register or interact with a Module that is not complete
 *
 * @package Sm\Core\Module\Error
 */
class IncompleteModuleException extends InvalidModuleException implements DevelopmentError {
    
}