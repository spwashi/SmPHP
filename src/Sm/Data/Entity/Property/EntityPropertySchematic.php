<?php


namespace Sm\Data\Entity\Property;


use Sm\Data\Property\PropertySchematic;

class EntityPropertySchematic extends PropertySchematic {
    protected $derivedFrom;
    public function load($configuration) {
        parent::load($configuration);
        $this->_configArraySet__derivedFrom($configuration);
        return $this;
    }
    protected function _configArraySet__derivedFrom($configuration) {
        $derivedFrom = $configuration['derivedFrom'] ?? [];
        if (isset($derivedFrom)) $this->setDerivedFrom($derivedFrom);
    }
    public function setDerivedFrom($derivedFrom) {
        $this->derivedFrom = $derivedFrom;
        return $this;
    }
    public function jsonSerialize() {
        $properties = parent::jsonSerialize();
        if (isset($this->derivedFrom)) {
            $properties['derivedFrom'] = $this->derivedFrom;
        }
        return $properties;
    }
    /**
     * @return mixed
     */
    public function getDerivedFrom() {
        return $this->derivedFrom;
    }
}