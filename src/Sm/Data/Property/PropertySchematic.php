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
    protected $protoSmID   = '[Property]';
    protected $datatypeFactory;
    protected $length;
    protected $onModelUpdateValue;
    protected $defaultValue;
    protected $isGenerated = false;
    
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
        $this->_configArraySet__length($configuration);
        $this->_configArraySet__updateValue($configuration);
        $this->_configArraySet__defaultValue($configuration);
        $this->_configArraySet__isGenerated($configuration);
        
        return $this;
    }
    protected function _configArraySet__datatypes($configuration) {
        $datatypes = $configuration['datatypes'] ?? [];
        if (isset($datatypes)) $this->setDatatypes($datatypes);
    }
    protected function _configArraySet__length($configuration) {
        $length = $configuration['length'] ?? null;
        if (isset($length)) $this->setLength($length);
    }
    protected function _configArraySet__updateValue(array $configuration) {
        if (isset($configuration['updateValue'])) {
            $this->setOnModelUpdateValue($configuration['updateValue']);
        }
    }
    protected function _configArraySet__defaultValue(array $configuration) {
        if (isset($configuration['defaultValue'])) {
            $this->setDefaultValue($configuration['defaultValue']);
        }
    }
    protected function _configArraySet__isGenerated(array $configuration) {
        if (isset($configuration['isGenerated'])) {
            $this->setIsGenerated($configuration['isGenerated']);
        }
    }
    #
    ##  Getters and Setters
    public function setOnModelUpdateValue($onModelUpdateValue) {
        $this->onModelUpdateValue = $onModelUpdateValue;
        return $this;
    }
    public function getOnModelUpdateValue() {
        return $this->onModelUpdateValue;
    }
    public function getLength() {
        return $this->length;
    }
    public function setLength($length) {
        $this->length = intval($length);
        return $this;
    }
    public function setDefaultValue($defaultValue) {
        $this->defaultValue = $defaultValue;
        return $this;
    }
    public function setIsGenerated(bool $isGenerated): PropertySchematic {
        $this->isGenerated = $isGenerated;
        return $this;
    }
    public function isGenerated(): bool {
        return $this->isGenerated;
    }
    public function getDefaultValue() {
        return $this->defaultValue;
    }
    #
    ##  Serialization
    public function jsonSerialize() {
        $length = $this->getLength();
        $items  = [
            'smID'      => $this->getSmID(),
            'name'      => $this->getName(),
            'datatypes' => $this->_getDatatypes(),
        ];
        if (isset($length)) $items['length'] = $length;
        return $items;
    }
}