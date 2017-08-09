<?php


namespace Sm\Data\Property;


use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Core\SmEntity\StdSmEntitySchematicTrait;
use Sm\Data\Type\DatatypeFactory;

/**
 * Class PropertySchematic
 *
 * Represents the structure of a Property
 */
class PropertySchematic implements PropertySchema, SmEntitySchematic, \JsonSerializable {
    protected $protoSmID = '[Property]';
    protected $datatypeFactory;
    use PropertyTrait;
    use StdSmEntitySchematicTrait {
        load as protected _load_std;
    }
    
    #
    ##  Constructors/initialization
    public function __construct(DatatypeFactory $datatypeFactory = null) {
        $this->setDatatypeFactory($datatypeFactory);
    }
    public static function init() { return new static(...func_get_args()); }
    
    #
    ##  Configuration
    public function load($configuration) {
        $this->_load_std($configuration);
        $this->_configArraySet__datatypes($configuration);
        
        return $this;
    }
    protected function _configArraySet__datatypes($configuration) {
        $datatypes = $configuration['datatypes'] ?? [];
        if (isset($datatypes)) $this->setDatatypes($datatypes);
    }
    
    #
    ##  Getters and Setters
    
    #
    ##  Serialization
    public function jsonSerialize() {
        return [
            'smID'      => $this->getSmID(),
            'name'      => $this->getName(),
            'datatypes' => $this->_getDatatypes(),
        ];
    }
}