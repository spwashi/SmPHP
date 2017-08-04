<?php


namespace Sm\Data\Property;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Core\SmEntity\StdSmEntityTrait;

/**
 * Class PropertySchematic
 *
 * Represents the structure of a Property
 */
class PropertySchematic implements PropertySchema, SmEntitySchematic {
    protected $name;
    use StdSmEntityTrait;
    
    public static function init() { return new static(...func_get_args()); }
    
    public function load($configuration) {
        if (!is_array($configuration)) throw new UnimplementedError("Cannot configure schematic without array");
        if (isset($configuration['name'])) $this->setName($configuration['name']);
        return $this;
    }
    public function getName() { return $this->name; }
    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }
}