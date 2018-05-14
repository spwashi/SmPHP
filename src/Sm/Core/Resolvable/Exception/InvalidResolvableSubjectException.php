<?php


namespace Sm\Core\Resolvable\Exception;


class InvalidResolvableSubjectException extends UnresolvableException {
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}