<?php


namespace Sm\Core\SmEntity;


trait StdSchematicizedSmEntity {
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