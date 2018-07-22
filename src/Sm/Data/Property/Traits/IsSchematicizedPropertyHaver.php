<?php


namespace Sm\Data\Property\Traits;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\SmEntity\Exception\InvalidConfigurationException;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Property\PropertyHaverSchematic;
use Sm\Data\Property\PropertySchematic;


trait IsSchematicizedPropertyHaver {
	use PropertyHaver_traitTrait;

	protected function registerSchematicProperties(PropertyHaverSchematic $ownerSchematic): void {
		$array              = [];
		$owner              = $this->inheritingPropertyHaver();
		$propertySchematics = $ownerSchematic->getProperties();




		#   Set the properties on the PropertyContainer
		/** @var PropertyContainer $properties */
		$properties = $owner->getProperties();
		$properties->register($array);
	}
}