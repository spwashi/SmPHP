<?php


namespace Sm\Data\Entity\Property;


use Sm\Data\Property\PropertySchematic;

class EntityPropertySchematic extends PropertySchematic {
    const ROLE__VALUE = 'value';
    protected $derivedFrom;
    protected $role;
    protected $contextNames;
    public function load($configuration) {
        parent::load($configuration);
        $this->_configArraySet__derivedFrom($configuration);
        $this->_configArraySet__role($configuration);
        $this->_configArraySet__contextNames($configuration);
        return $this;
    }
    protected function _configArraySet__derivedFrom($configuration) {
        $derivedFrom = $configuration['derivedFrom'] ?? [];
        if (isset($derivedFrom)) $this->setDerivedFrom($derivedFrom);
    }
    protected function _configArraySet__contextNames($configuration) {
        $contexts = $configuration['contexts'] ?? [];
        if (isset($contexts)) $this->setContextNames($contexts);
    }
    protected function _configArraySet__role($configuration) {
        $role = $configuration['role'] ?? null;
        if (isset($role)) $this->setRole($role);
    }
    public function setDerivedFrom($derivedFrom) {
        $this->derivedFrom = $derivedFrom;
        return $this;
    }
    public function jsonSerialize() {
        $properties   = parent::jsonSerialize();
        $contextNames = $this->getContextNames();
        
        if (isset($this->derivedFrom)) {
            $properties['derivedFrom'] = $this->derivedFrom;
        }
        if (isset($contextNames)) {
            $properties['contexts'] = $contextNames;
        }
        
        return $properties;
    }
    /**
     * @return mixed
     */
    public function getDerivedFrom() {
        return $this->derivedFrom;
    }
    public function setRole(string $role) {
        $this->role = $role;
        return $this;
    }
    public function getRole(): ?string {
        return $this->role;
    }
    public function getContextNames(): ?array {
        return count($this->contextNames) ? $this->contextNames : null;
    }
    public function setContextNames(array $contexts) {
        $this->contextNames = $contexts;
        return $this;
    }
}