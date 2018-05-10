<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:23 AM
 */

namespace Sm\Data\Model;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Data\Property\PropertySchematicInstantiator;

/**
 * Class ModelFactory
 * @method Model resolve($name = null)
 */
class ModelFactory extends SmEntityFactory {
    /** @var PropertySchematicInstantiator $propertySchematicInstantiator */
    protected $propertySchematicInstantiator;
    public function __construct() {
        parent::__construct();
        $this->register(null, [ $this, 'resolveDefault' ]);
    }
    protected function canCreateClass($object_type) {
        return parent::canCreateClass($object_type) && is_a($object_type, Model::class);
    }
    /**
     * @param null $model
     *
     * @return $this|\Sm\Data\Model\Model
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     */
    public function resolveDefault($model = null) {
        if (!isset($model)) {
            return new Model($this->propertySchematicInstantiator);
        } else {
            if ($model instanceof ModelSchema) {
                if ($model instanceof Model) return clone $model;
                else return (new Model($this->propertySchematicInstantiator))->fromSchematic($model);
            }
            throw new InvalidArgumentException("Cannot instantiate model with parameters");
        }
    }
    public function setPropertyInstantiatior(PropertySchematicInstantiator $propertySchematicInstantiator) {
        $this->propertySchematicInstantiator = $propertySchematicInstantiator;
        return $this;
    }
}