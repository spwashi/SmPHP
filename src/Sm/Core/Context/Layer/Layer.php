<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 10:51 AM
 */

namespace Sm\Core\Context\Layer;

use Sm\Core\Context\Context;


/**
 * Class Layer
 *
 * A layer represents a set of Modules and core classes that work together to fulfill one
 * particular realm of functionality. They are meant to more explicitly structure code
 * around modularity and loose coupling.
 *
 * Each Layer only exposes Modules that other layers can use to interact with them.
 * The goal is to discourage code that is too powerful.
 *
 * @package Sm\Core\Context
 */
interface Layer extends Context {
}