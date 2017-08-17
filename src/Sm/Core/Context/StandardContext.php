<?php
/**
 * User: Sam Washington
 * Date: 6/26/17
 * Time: 10:12 PM
 */

namespace Sm\Core\Context;


use Sm\Core\Container\Mini\MiniContainer;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;

/**
 * Class AbstractContext
 *
 * The general stuff that most Contexts will have in common
 *
 * @package Sm\Core\Context
 *
 */
abstract class StandardContext implements Context {
    use HasObjectIdentityTrait;
    /**
     * @var  \Sm\Core\Container\Container A Container for the Attributes of this Context that make it special
     *                                    They are key identifiers of this context
     */
    protected $items;
    
    /**
     * AbstractContext constructor.
     *
     */
    public function __construct() {
        $this->items = MiniContainer::init();
        $this->createSelfID();
    }
}