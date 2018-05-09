<?php


namespace Sm\Data\Entity\Property;


use Sm\Data\Entity\Entity;
use Sm\Data\Property\Property;
use Sm\Data\Type\Undefined_;

class EntityAsProperty extends Property {
    /**
     * @var bool Have we "found" the entity?
     */
    protected $found = false;
    /** @var Entity $subject */
    protected $subject;
    protected $identity;
    /** @var Entity $entity */
    protected $entity;
    public function find() {
        if ($this->found) {
            return $this->entity;
        } else {
            $this->found = true;
            return $this->entity->find($this->identity);
        }
    }
    public function setSubject($subject) {
        if ($subject instanceof Undefined_) return parent::setSubject($subject);
        $effectiveSchematic = $this->entity->getEffectiveSchematic();
        if ($effectiveSchematic) {
            /** @var \Sm\Data\Entity\EntitySchematic $derivedFrom */
            $derivedFrom = $effectiveSchematic->properties;
//            if ($derivedFrom) var_dump($derivedFrom);
        }
        return $this;
    }
    public function setEntity(Entity $entity) {
        $this->entity = $entity;
        return $this;
    }
    public function resolve() {
        return $this->entity;
    }
    public function jsonSerialize() {
        return [
            'val' => $this->entity,
        ];
    }
    public function setIdentity($identity) {
        $this->identity = $identity;
        return $this;
    }
}