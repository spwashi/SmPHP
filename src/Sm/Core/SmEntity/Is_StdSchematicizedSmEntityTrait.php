<?php


namespace Sm\Core\SmEntity;

/**
 * Trait StdSchematicizedSmEntity
 *
 * For SmEntities that we would identify as being Schematicized (schema,schematic,
 */
trait Is_StdSchematicizedSmEntityTrait {
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
        
        if ($schematic instanceof SmEntitySchematic && property_exists($this, '_smID'))
            $this->_smID = $this->_smID ?? $schematic->getSmID();
        
        return $this;
    }
}