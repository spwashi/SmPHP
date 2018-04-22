<?php


namespace Sm\Data\Property;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Core\SmEntity\StdSmEntitySchematicTrait;
use Sm\Data\Type\DatatypeFactory;

/**
 * Class PropertySchematic
 *
 * Represents the structure of a Property
 */
class PropertySchematic implements PropertySchema, SmEntitySchematic, \JsonSerializable {
    protected $datatypeFactory;
    protected $length;
    /** @var ReferenceDescriptorSchematic $referenceDescriptor */
    protected $referenceDescriptor;
    protected $onModelUpdateValue;
    protected $defaultValue;
    protected $isGenerated = false;
    public function __construct(DatatypeFactory $datatypeFactory = null) {
        $this->setDatatypeFactory($datatypeFactory);
    }
    public static function init() { return new static(...func_get_args()); }
    
    use PropertyTrait;
    use StdSmEntitySchematicTrait {
        load as protected _load_std;
    }
    
    #
    ##  Constructors/initialization
    
    #
    ##  Configuration
    public function load($configuration) {
        $this->_load_std($configuration);
        $this->_configArraySet__datatypes($configuration);
        $this->_configArraySet__length($configuration);
        $this->_configArraySet__updateValue($configuration);
        $this->_configArraySet__defaultValue($configuration);
        $this->_configArraySet__isGenerated($configuration);
        $this->_configArraySet__reference($configuration);
        
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
    /**
     * @param array $configuration
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function _configArraySet__reference(array $configuration) {
        if (isset($configuration['reference'])) {
            $reference = $configuration['reference'];
            if (!is_array($reference)) {
                throw new InvalidArgumentException('Cannot reference -- ' . json_encode($reference));
            }
            $this->setReferenceDescriptor(new ReferenceDescriptorSchematic($reference['hydrationMethod'] ?? null, $reference['identity'] ?? null));
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
    public function getReferenceDescriptor(): ?ReferenceDescriptorSchematic {
        return $this->referenceDescriptor;
    }
    public function setReferenceDescriptor(ReferenceDescriptorSchematic $referenceDescriptor): PropertySchematic {
        $this->referenceDescriptor = $referenceDescriptor;
        return $this;
    }
    
    
    #
    ##  Serialization
    public function jsonSerialize() {
        $length                       = $this->getLength();
        $referenceDescriptorSchematic = $this->getReferenceDescriptor();
        $datatypes                    = $this->_getDatatypes();
        $items                        = [
            'smID'      => $this->getSmID(),
            'name'      => $this->getName(),
            'datatypes' => $datatypes,
        ];
        if (!in_array('null', $datatypes ?? [])) {
            $items['isRequired'] = true;
        }
        if (isset($length)) $items['length'] = $length;
        if (isset($referenceDescriptorSchematic)) $items['reference'] = $referenceDescriptorSchematic;
        return $items;
    }
}