<?php


namespace Sm\Core\Schema;


/**
 * Interface Schematicized
 *
 * Something that has a Schematic from which it can be initialized
 */
interface Schematicized {
    public function fromSchematic($schematic);
    public function getEffectiveSchematic();
}