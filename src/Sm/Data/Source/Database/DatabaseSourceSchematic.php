<?php


namespace Sm\Data\Source\Database;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\SmEntity\StdSmEntityTrait;
use Sm\Data\Source\DataSourceSchematic;

class DatabaseSourceSchematic implements DatabaseSourceSchema, DataSourceSchematic {
    use StdSmEntityTrait;
    protected $name;
    public static function init() { return new static(...func_get_args()); }
    public function getName() {
        return $this->name;
    }
    /**
     * @param mixed $name
     *
     * @return DatabaseSourceSchematic
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    public function load($configuration) {
        if (!is_array($configuration)) {
            throw new UnimplementedError("Cannot configure schematic using something other than an array");
        }
        if (isset($configuration['name'])) {
            $this->setName($configuration['name']);
        }
        return $this;
    }
}