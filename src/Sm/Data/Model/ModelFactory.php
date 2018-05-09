<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:23 AM
 */

namespace Sm\Data\Model;


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
    public function resolveDefault($parameters = null) {
        return !isset($parameters) ? new Model($this->propertySchematicInstantiator) : null;
    }
    public function setPropertyInstantiatior(PropertySchematicInstantiator $propertySchematicInstantiator) {
        $this->propertySchematicInstantiator = $propertySchematicInstantiator;
        return $this;
    }
}