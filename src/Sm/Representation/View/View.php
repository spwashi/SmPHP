<?php


namespace Sm\Representation\View;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Representation\Representation;

/**
 * Class View
 *
 * Represents something that can be displayed on the screen (somehow)
 */
abstract class View implements Representation {
    use HasObjectIdentityTrait;
    
    /**
     * Represent whatever we are depicting as a string
     *
     * @return string
     */
    abstract public function render(): string;
}