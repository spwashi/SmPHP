<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/28/18
 * Time: 6:50 AM
 */

namespace Sm\Core\SmEntity\Traits;


use Sm\Data\Property\PropertySchematicContainer;

/**
 *  For items that have an PropertySchematicContainer
 */
trait HasPropertySchematicsTrait {
	public function getProperties($property_names = []): PropertySchematicContainer {
		$properties = $this->properties = $this->properties ?? PropertySchematicContainer::init();

		if (count($property_names)) {
			$return_properties = [];
			foreach ($property_names as $name) {
				$return_properties[$name] = $properties->{$name};
			}
			return PropertySchematicContainer::init()->register($return_properties);
		}

		return $properties;
	}
	public function setProperties(PropertySchematicContainer $properties) {
		$this->properties = $properties;
		return $this;
	}
}