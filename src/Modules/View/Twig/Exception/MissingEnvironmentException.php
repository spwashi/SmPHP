<?php


namespace Sm\Modules\View\Twig\Exception;


use Sm\Core\Module\Error\InvalidModuleException;

/**
 * Class MissingEnvironmentException
 *
 * Exception thrown when a TwigView does not have an Environment from which to render
 */
class MissingEnvironmentException extends InvalidModuleException {
    
}