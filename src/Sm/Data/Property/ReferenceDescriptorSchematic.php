<?php


namespace Sm\Data\Property;

use Sm\Core\Schema\Schematic;

/**
 * Class PropertyAsReferenceDescriptorSchematic
 */
class ReferenceDescriptorSchematic implements Schematic, \JsonSerializable {
    /**
     * Describes the way we'd hydrate ReferenceDescriptors
     *
     * @var null|string
     */
    protected $hydrationMethod;
    /**
     * ReferenceDescriptorSchematic constructor.
     *
     * @param string|null $hydrationMethod
     */
    public function __construct($hydrationMethod = null) {
        $this->hydrationMethod = $hydrationMethod;
    }
    
    public function jsonSerialize() {
        return [
            'hydrationMethod' => $this->hydrationMethod,
        ];
    }
    /**
     * @return null|string
     */
    public function getHydrationMethod() {
        return $this->hydrationMethod;
    }
}