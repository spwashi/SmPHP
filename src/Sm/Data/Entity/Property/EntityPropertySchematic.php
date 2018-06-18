<?php


namespace Sm\Data\Entity\Property;


use Sm\Data\Property\PropertySchematic;

class EntityPropertySchematic extends PropertySchematic implements EntityPropertySchema {
	const ROLE__VALUE = 'value';
	protected $derivedFrom;
	protected $role;
	protected $minLength;
	protected $contextNames;
	public function load($configuration) {
		parent::load($configuration);
		$this->_configArraySet__derivedFrom($configuration);
		$this->_configArraySet__role($configuration);
		$this->_configArraySet__contextNames($configuration);
		$this->_configArraySet__minLength($configuration);
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
	protected function _configArraySet__minLength($configuration) {
		$minLength = $configuration['minLength'] ?? null;
		if (isset($minLength)) $this->setMinLength($minLength);
	}
	public function setDerivedFrom($derivedFrom) {
		$this->derivedFrom = $derivedFrom;
		return $this;
	}
	public function jsonSerialize() {
		$attributes = [
			'smID'        => $this->getSmID(),
			'datatypes'   => $this->getRawDatatypes(),
			'isRequired'  => $this->isRequired(),
			'isGenerated' => $this->isGenerated(),
			'role'        => $this->getRole(),
			'length'      => $this->getLength(),
			'minLength'   => $this->getMinLength(),
			'derivedFrom' => $this->getDerivedFrom(),
			'reference'   => $this->getReferenceDescriptor(),
		];
		return array_filter($attributes, function ($item) { return !is_null($item); });
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
	public function setMinLength($minLength) {
		$this->minLength = $minLength;
		return $this;
	}
	public function getMinLength(): ?int {
		return $this->minLength;
	}
	public function getContextNames(): ?array {
		return count($this->contextNames) ? $this->contextNames : null;
	}
	public function setContextNames(array $contexts) {
		$this->contextNames = $contexts;
		return $this;
	}
}