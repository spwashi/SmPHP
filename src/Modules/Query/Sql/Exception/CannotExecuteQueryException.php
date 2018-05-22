<?php


namespace Modules\Query\Sql\Exception;


use Sm\Core\Exception\Exception;
use Sm\Query\Exception\CannotQueryException;

class CannotExecuteQueryException extends Exception implements CannotQueryException {
    public function jsonSerialize() {
        try {
            return parent::jsonSerialize();
        } catch (\Throwable$exception) {
            return [ 'message' => $this->getMessage() ];
        }
    }
    
}