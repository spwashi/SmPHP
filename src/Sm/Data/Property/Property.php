<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:30 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Abstraction\Readonly_able;
use Sm\Core\Abstraction\ReadonlyTrait;
use Sm\Core\SmEntity\SmEntity;
use Sm\Core\SmEntity\StdSmEntityTrait;
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
 * @property-read string                           $name
 * @property-read \Sm\Data\Property\Property|false $reference
 * @property-read string                           $object_id
 * @property-read array                            $potential_types
 */
class Property extends Variable_ implements Readonly_able, PropertySchema, SmEntity {
    use ReadonlyTrait, StdSmEntityTrait;
    protected $protoSmID = 'Property';
    /** @var  string $name */
    protected $_name;
    
    ####################################################
    #   Getters and Setters
    ####################################################
    public function __get($name) {
        if ($name === 'object_id') return $this->getObjectId();
        if ($name === 'potential_types') return $this->getPotentialTypes();
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
    /**
     * @return string
     */
    public function getName(): string {
        return $this->_name;
    }
    /**
     * Set the name of the property
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name) {
        $this->_name = $name;
        return $this;
    }
}