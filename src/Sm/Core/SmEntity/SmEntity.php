<?php
/**
 * User: Sam Washington
 * Date: 8/2/17
 * Time: 10:12 PM
 */

namespace Sm\Core\SmEntity;
use Sm\Core\Schema\Schematicized;


/**
 * Interface SmEntity
 *
 * Class that represents a Framework Entity.
 * These are the objects/data structures that we will likely see in each implementation of this Framework.
 *
 * Meant to be a concrete implementation of the SmEntitySchema  interface
 */
interface SmEntity extends SmEntitySchema, Schematicized {
}