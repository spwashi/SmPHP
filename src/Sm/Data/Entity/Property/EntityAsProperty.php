<?php


namespace Sm\Data\Entity\Property;


use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Entity\Entity;
use Sm\Data\Property\Property;
use Sm\Data\Type\Undefined_;

class EntityAsProperty extends EntityProperty {
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
    /**
     * @param $subject
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function setSubject($subject) {
        if ($subject instanceof Undefined_) return parent::setSubject($subject);
        
        $effectiveSchematic = $this->entity->getEffectiveSchematic();
        
        if (!$effectiveSchematic) throw new UnimplementedError("Cannot set subject of entities without Schematics");
        
        /** @var \Sm\Data\Property\PropertySchemaContainer $propertySchematics */
        $propertySchematics = $effectiveSchematic->properties;
        foreach ($propertySchematics as $propertySchematic) {
            if (!($propertySchematic instanceof EntityPropertySchematic)) continue;
            if ($propertySchematic->getRole() === EntityPropertySchematic::ROLE__VALUE) {
                $propertySmID = $propertySchematic->getSmID();
                $property     = $this->entity->properties->{$propertySmID};
                if ($property) {
                    $property->value = $subject;
                    return $this;
                }
            }
        }
        throw new UnimplementedError("Can only set subjects of entities that have a property with a 'value' role");
    }
    public function setEntity(Entity $entity) {
        $this->entity = $entity;
        return $this;
    }
    public function resolve() {
        return $this->entity;
    }
    public function jsonSerialize() {
        return $this->entity;
    }
    public function setIdentity($identity) {
        $this->identity = $identity;
        return $this;
    }
}