<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:30 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Abstraction\Readonly_able;
use Sm\Core\Abstraction\ReadonlyTrait;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Internal\Monitor\History;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntity;
use Sm\Core\SmEntity\StdSchematicizedSmEntity;
use Sm\Core\SmEntity\StdSmEntityTrait;
use Sm\Data\Property\Event\PropertyValueChange;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\Type\Variable_\Variable_;

/**
 * Class Property
 *
 * Represents a property held by an Entity or Model
 *
 * @mixin ReadonlyTrait
 * @mixin Variable_
 *
 * @package Sm\Data\Property
 *
 * @property-read \Sm\Core\Internal\Monitor\History $valueHistory              A History of this Property & it's values
 * @property-read string                            $object_id
 * @property-read array                             $potential_types
 */
class Property extends Variable_ implements Readonly_able,
                                            PropertySchema,
                                            Schematicized,
                                            SmEntity,
                                            \JsonSerializable {
    use ReadonlyTrait;
    use StdSmEntityTrait;
    use PropertyTrait;
    use StdSchematicizedSmEntity {
        fromSchematic as protected _fromSchematic_std;
    }
    /** @var  \Sm\Core\Internal\Monitor\History $valueHistory */
    protected $valueHistory;
    public function __construct($name = null) {
        $this->valueHistory = new History;
        parent::__construct($name);
    }
    
    #
    ##   Getters and Setters
    public function __get($name) {
        if ($name === 'object_id') return $this->getObjectId();
        if ($name === 'potential_types') return $this->getPotentialTypes();
        if ($name === 'history') return $this->getValueHistory();
        
        return parent::__get($name);
    }
    /**
     * Setter for this Property
     *
     * @param $name
     * @param $value
     *
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     */
    public function __set($name, $value) {
        if ($this->isReadonly()) throw new ReadonlyPropertyException("Cannot modify a readonly property");
        parent::__set($name, $value);
    }
    public function setSubject($subject) {
        parent::setSubject($subject);
        $this->valueHistory->append(PropertyValueChange::init($this, $subject));
        return $this;
    }
    /**
     * Get a History of the values held by this Property (in this session?)
     *
     * @return \Sm\Core\Internal\Monitor\History
     */
    public function getValueHistory(): History {
        return $this->valueHistory;
    }
    
    #
    ##  Initialization
    public function fromSchematic($schematic) {
        /** @var \Sm\Data\Property\PropertySchematic $schematic */
        $this->_fromSchematic_std($schematic);
        
        $rawDataTypes = $this->getRawDataTypes();
        $name         = $this->getName() ?? ($schematic ? $schematic->getName() : null);
        if (isset($name)) $this->setName($name);
        $this->setDatatypes(count($rawDataTypes) ? $rawDataTypes : ($schematic ? $schematic->getRawDataTypes() : null));
        return $this;
    }
    protected function checkCanUseSchematic($schematic) {
        if (!($schematic instanceof PropertySchema)) {
            throw new InvalidArgumentException("Can only initialize Properties using PropertySchematics");
        }
    }
    
    
    #
    ##  Debugging/Serialization
    public function jsonSerialize() {
        return [
            'smID'      => $this->getSmID(),
            'name'      => $this->getName(),
            'datatypes' => $this->getRawDataTypes(),
            'value'     => $this->resolve(),
        ];
    }
    public function __debugInfo() {
        return $this->jsonSerialize();
    }
}