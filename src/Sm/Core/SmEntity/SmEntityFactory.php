<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:13 AM
 */

namespace Sm\Core\SmEntity;


use Sm\Core\Factory\StandardFactory;
use Sm\Core\Schema\Schematic;
use Sm\Core\Schema\Schematicized;

/**
 * Class SmEntityFactory
 *
 * Factory to create or resolve references to SmEntities
 *
 */
abstract class SmEntityFactory extends StandardFactory {
    public function build($name = null, $schematic = null) {
        /** @var \Sm\Core\SmEntity\SmEntity $item */
        $item = parent::build(...func_get_args());
        
        # SmEntities are usually going to be schematicized, so I don't feel bad that this is here
        if ($schematic instanceof Schematic && $item instanceof Schematicized) {
            return $item->fromSchematic($schematic);
        }
        
        return $item;
    }
    
    
    protected function canCreateClass($object_type) {
        return is_a($object_type, SmEntity::class, true);
    }
}