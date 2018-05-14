<?php


namespace Sm\Data\Entity\Context;


use Sm\Core\Context\StandardContext;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Entity\EntitySchematic;
use Sm\Data\Property\PropertySchemaContainer;

class EntityContext extends StandardContext implements \JsonSerializable {
    protected $context_name;
    /** @var \Sm\Data\Entity\EntitySchematic[] */
    protected $entitySchematics;
    
    public function __construct(string $context_name = null) {
        parent::__construct($context_name);
        $this->context_name = $context_name;
    }
    public static function init(string $context_name = null) {
        return new static($context_name);
    }
    public function getContextName(): ?string {
        return $this->context_name;
    }
    /**
     * @param array $entitySchematics
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function registerSchematicArray(array $entitySchematics) {
        foreach ($entitySchematics as $entitySchematic) {
            if (!($entitySchematic instanceof EntitySchematic)) throw new InvalidArgumentException("Can only register Entity Schematics");
            $this->registerEntitySchematic($entitySchematic);
        }
        return $this;
    }
    /**
     * @param \Sm\Data\Entity\EntitySchematic $entitySchematic
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function registerEntitySchematic(EntitySchematic $entitySchematic) {
        $smID = $entitySchematic->getSmID();
        
        if (!isset($smID)) throw new InvalidArgumentException("Can't identify schematic without SmID");
        
        $this->entitySchematics[ $smID ] = $entitySchematic;
        
        return $this;
    }
    /**
     * @param string $name
     *
     * @return null|\Sm\Data\Entity\Context\ContextualizedEntityProxy
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function getSchematic(string $name): ?ContextualizedEntityProxy {
        if (isset($this->entitySchematics[ $name ])) return $this->entitySchematics[ $name ]->proxyInContext($this);
        
        foreach ($this->entitySchematics as $schematic) {
            if ($name === $schematic->getSmID()) return $schematic->proxyInContext($this);
        }
        return null;
    }
    /**
     */
    public function getRegisteredEntitySchematics(): PropertySchemaContainer {
        return new PropertySchemaContainer($this->entitySchematics);
    }
    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize() {
        $schematics = [];
        foreach ($this->entitySchematics as $index => $schematic) {
            $schematics[ $index ] = $this->getSchematic($index);
        }
        return [
            'name'       => $this->getContextName(),
            'schematics' => $schematics,
        ];
    }
}