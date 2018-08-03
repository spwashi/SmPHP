<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:11 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Abstraction\ReadonlyTrait;
use Sm\Core\Container\Container;
use Sm\Core\Container\Mini\MiniContainer;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Resolvable\NativeResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Data\Property\Exception\ReadonlyPropertyException;
use Sm\Data\SmEntity\SmEntityDataManager;

/**
 * Class PropertyContainer
 *
 * Container for PropertySchemas
 *
 * @package Sm\Data\Property
 * @property \Sm\Data\Property\PropertySchema $id
 */
class PropertySchemaContainer extends MiniContainer {
    use ReadonlyTrait;


    public function __clone() {
        foreach ($this->registry as $key => &$item) {
            $this->registry[$key] = (clone $item);
        }
    }
    public function current(): PropertySchema {
        $item = $this->{$this->getRegistryName()}[$this->key()];
        if ($item instanceof NativeResolvable) return $item->resolve();
        return $item;
    }

    /** @return PropertySchema */
    public function resolve($name = null) {
        $parsed = SmEntityDataManager::parseSmID($name);
        foreach ($this as $propertyName => $property) {
            if (!$parsed) continue;
            $smID = $property->getSmID();
            if ($smID === $name) return $property;
            $normalized_smID       = static::normalizePropertySmID($smID);
            $normalized_nameAsSmID = static::normalizePropertySmID($name);
            if ($normalized_smID === $normalized_nameAsSmID) {
                return $property;
            }
        }
        return parent::resolve($name);
    }
    protected function getResolvedValue(Resolvable $item, $args): ?PropertySchema {
        return $item instanceof PropertySchema ? $item : $item->resolve();
    }

    /**
     * Add a Property to this class, naming it if it is not already named.
     *
     * @param \Sm\Data\Property\PropertySchema|string $name
     * @param \Sm\Data\Property\PropertySchema $registrant
     *
     * @return $this
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException If we try to add a property to
     *                                                                  this PropertyContainer while the
     *                                                                  readonly flag is set
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException If we try to register anything
     *                                          that isn't a named Property or array of named properties
     */
    public function register($name = null, $registrant = null) {
        # Don't register to readonly PropertyContainers
        if ($this->readonly) {
            throw new ReadonlyPropertyException("Trying to add a property to a readonly PropertyContainer.");
        }

        # Iterate through an array if we're registering multiple at a time
        if (is_array($name)) {
            foreach ($name as $index => $item) {
                $index = !is_numeric($index) ? $index : null;
                $this->register($index, $item);
            }
            return $this;
        }

        # If the first parameter is a named property, register it
        if ($name instanceof PropertySchema && isset($name->name)) {
            if (isset($registrant)) throw new InvalidArgumentException("When using a PropertySchema as the first parameter, ther can be no second");
            return $this->register($name->name, $name);
        }

        # We can only register Properties
        $this->checkRegistrantIsCorrectType($registrant);


        # We can only register named Properties
        if (!isset($name)) {
            throw new InvalidArgumentException("Must name properties.");
        }

        # Set the name of the property based on this one
        if (isset($registrant) && !isset($registrant->name)) $registrant->setName($name);

        /** @var static $result */
        parent::register($name, $registrant);
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
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException If we try to remove a property while this class has been marked as readonly
     */
    public function remove($name) {
        if ($this->readonly) {
            throw new ReadonlyPropertyException("Cannot remove elements from a readonly PropertyContainer.");
        }
        return parent::remove($name);
    }
    /**
     * @param $registrant
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function checkRegistrantIsCorrectType($registrant): void {
        if (!($registrant instanceof PropertySchema)) {
            throw new InvalidArgumentException("Can only add Properties to the PropertyContainer");
        }
    }
    public function jsonSerialize() {
        return $this->registry;
    }
    public function __debugInfo() {
        return $this->jsonSerialize();
    }
    /**
     * @param $smID
     *
     * @return mixed
     */
    protected static function normalizePropertySmID($smID): string {
        return str_replace(' ', '', $smID);
    }
}