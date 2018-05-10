<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:23 AM
 */

namespace Sm\Data\Property;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Data\Type\DatatypeFactory;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * Class PropertyFactory
 * @method Property resolve($name = null)
 */
class PropertyFactory extends SmEntityFactory {
    public function __construct(DatatypeFactory $datatypeFactory = null) {
        parent::__construct();
        if (isset($datatypeFactory)) $this->setDatatypeFactory($datatypeFactory);
        $this->register(null, [ $this, 'resolveDefault' ]);
    }
    public function setDatatypeFactory(DatatypeFactory $datatypeFactory) {
        $this->datatypeFactory = $datatypeFactory;
        return $this;
    }
    /**
     * @param object|string $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        return parent::canCreateClass($object_type) && is_a($object_type, Property::class);
    }
    /**
     * @param null $parameters
     *
     * @return \Sm\Data\Property\Property
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function resolveDefault($parameters = null) {
        if (!isset($parameters)) {
            return new Property;
        } else {
            throw new InvalidArgumentException("Cannot instantiate property with parameters");
        }
    }
}