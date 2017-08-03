<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:11 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Abstraction\ReadonlyTrait;
use Sm\Core\Container\Container;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Property\Exception\ReadonlyPropertyException;

/**
 * Class PropertyContainer
 *
 * Container for Properties, held by Models and Entities
 *
 * @package Sm\Data\Property
 * @method Property current()
 * @property \Sm\Data\Property\Property $id
 */
class PropertyContainer extends Container {
    use ReadonlyTrait;
    
    /** @var  PropertyHaver $PropertyHaver Whatever these properties belong to */
    protected $PropertyHaver;
    /** @var  \Sm\Data\Source\DataSource $Source If there is a source that all of these Properties should belong to */
    protected $Source;
    
    /**
     * Rules for cloning the PropertyContainer
     */
    public function __clone() {
        foreach ($this->registry as $key => &$item) {
            $this->registry[ $key ] = (clone $item);
        }
        $this->addPropertyPropertyHavers(null);
    }
    /**
     * @param null|string $name
     *
     * @return \Sm\Data\Property\Property
     */
    public function resolve($name = null) {
        return $this->getItem($name);
    }
    /**
     * Add a Property to this class, naming it if it is not already named.
     *
     * @param \Sm\Data\Property\Property|string $name
     * @param \Sm\Data\Property\Property        $registrand
     *
     * @return $this
     * @throws \Sm\Data\Property\ReadonlyPropertyException If we try to add a property to
     *                                                       this PropertyContainer while the
     *                                                       readonly flag is set
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException If we try to register anything
     *                                          that isn't a named Property or array of named properties
     */
    public function register($name = null, $registrand = null) {
        # Don't register to readonly PropertyContainers
        if ($this->readonly) throw new ReadonlyPropertyException("Trying to add a property to a readonly PropertyContainer.");
    
        # Iterate through an array if we're registering multiple at a time
        if (is_array($name)) {
            foreach ($name as $index => $item) {
                $this->register(!is_numeric($index) ? $index : null, $item);
            }
            return $this;
        }
    
        # If the first parameter is a named property, register it
        if ($name instanceof Property && isset($name->name)) return $this->register($name->name, $name);
    
        # We can only register Properties
        if (!($registrand instanceof Property)) throw new InvalidArgumentException("Can only add Properties to the PropertyContainer");
    
    
        # We can only register named Properties
        if (!isset($name)) throw new InvalidArgumentException("Must name properties.");
    
    
        # Set the name of the property based on this one
        if (!isset($registrand->name)) $registrand->setName($name);
        
        /** @var static $result */
        parent::register($name, $registrand);
        return $this;
    }
    /**
     * Remove an element from this property container.
     * Return that element
     *
     * @param string $name The name of the variable that we want to remove
     *
     * @return mixed The variable that we removed
     *
     * @throws \Sm\Data\Property\ReadonlyPropertyException If we try to remove a property while this class has been marked as readonly
     */
    public function remove($name) {
        if ($this->readonly) {
            throw new ReadonlyPropertyException("Cannot remove elements from a readonly PropertyContainer.");
        }
        return parent::remove($name);
    }
    /**
     * Get the PropertyHaver of these Properties
     *
     * @return PropertyHaver|null
     */
    public function getPropertyHaver() {
        return $this->PropertyHaver;
    }
}