<?php


namespace Sm\Core\SmEntity\Traits;

use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntitySchema;

/**
 * Trait StdSchematicizedSmEntity
 *
 * For SmEntities that we would identify as being Schematicized (schema,schematic,
 */
trait Is_StdSchematicizedSmEntityTrait {
    protected $effectiveSchematic;
    public function getEffectiveSchematic() {
        return $this->effectiveSchematic;
    }
    /**
     *
     * @param $schematic
     *
     * @return
     */
    abstract public function checkCanUseSchematic($schematic);
    public function fromSchematic($schematic) {
        if (!$schematic) return $this;
        $this->checkCanUseSchematic($schematic);
        
        if ($schematic instanceof SmEntitySchema) {
            $this->_smID = $this->_smID ?? $schematic->getSmID();
        }

        $this->effectiveSchematic = $schematic instanceof Schematicized ? $schematic->getEffectiveSchematic() : $schematic;
        
        return $this;
    }
}