<?php


namespace Sm\Data\Source\Database;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\SmEntity\Traits\Is_StdSmEntitySchematicTrait;
use Sm\Data\Source\DataSourceSchematic;

class DatabaseSourceSchematic implements DatabaseSourceSchema, DataSourceSchematic, \JsonSerializable {
    use Is_StdSmEntitySchematicTrait;
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
    
    function jsonSerialize() {
        return [ 'smID' => $this->getSmID(),
                 'name' => $this->getName(), ];
    }
}