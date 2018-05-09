<?php


namespace Sm\Core\Schema;


/**
 * Interface SchematicInstantiator
 *
 * Converts Schematics to entities
 */
interface SchematicInstantiator {
    /**
     * @param \Sm\Core\Schema\Schematic $schematic
     *
     * @return \Sm\Core\Schema\Schematicized
     */
    public function instantiate($schematic);
}