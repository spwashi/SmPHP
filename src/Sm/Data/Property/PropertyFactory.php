<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:23 AM
 */

namespace Sm\Data\Property;


use Sm\Core\SmEntity\SmEntityFactory;

class PropertyFactory extends SmEntityFactory {
    public function __construct() {
        parent::__construct();
        $this->register(null, [ $this, 'resolveDefault' ]);
    }
    
    /**
     * @param object|string $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        return parent::canCreateClass($object_type) && is_a($object_type, Property::class);
    }
    public function resolveDefault($parameters = null) {
        return !isset($parameters) ? new Property : null;
    }
}