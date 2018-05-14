<?php


namespace Modules\Query\MySql\Authentication\Exception;


use Sm\Authentication\Exception\InvalidAuthenticationException;
use Sm\Core\Exception\FatalException;

class InvalidMysqlAuthenticationException extends InvalidAuthenticationException implements FatalException {
    
}