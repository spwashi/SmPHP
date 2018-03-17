<?php


namespace Sm\Data\Entity;


use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Core\SmEntity\StdSmEntitySchematicTrait;

/**
 * Class EntitySchematic
 *
 * Represents the structure of a Entity
 */
class EntitySchematic implements EntitySchema, SmEntitySchematic, \JsonSerializable {
    protected $datatypeFactory;
    protected $length;
    protected $onModelUpdateValue;
    protected $defaultValue;
    protected $isGenerated = false;
    
    public static function init(): EntitySchematic { return new static; }
    
    use EntityTrait;
    use StdSmEntitySchematicTrait {
        load as protected _load_std;
    }
    
    #
    ##  Configuration
    public function load($configuration) {
        $this->_load_std($configuration);
        
        return $this;
    }
    public function jsonSerialize() {
        $items = [
            'smID' => $this->getSmID(),
            'name' => $this->getName(),
        ];
        return $items;
    }
}