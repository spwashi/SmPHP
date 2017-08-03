<?php
/**
 * User: Sam Washington
 * Date: 6/24/17
 * Time: 6:17 PM
 */

namespace Sm\Core\Hook;

use Sm\Core\Resolvable\Resolvable;

/**
 * Interface Hook
 *
 * Represents something (usually a resolvable) that acts like a hook.
 * Hooks are essentially functions run at a specific time in an object's lifecycle
 * that may or may not affect the way the rest of the process works.
 *
 * @package Sm\Core\Hook
 */
interface Hook extends Resolvable {
    const INIT       = 'init';
    const DEACTIVATE = 'deactivate';
    const RESET      = 'reset';
    const CHECK      = 'check';
}