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
    protected $identity;
    /**
     * ReferenceDescriptorSchematic constructor.
     *
     * @param string|null $hydrationMethod
     * @param null        $identity
     */
    public function __construct($hydrationMethod = null, $identity = null) {
        $this->hydrationMethod = $hydrationMethod;
        $this->identity        = $identity;
    }
    
    public function jsonSerialize() {
        return [
            'hydrationMethod' => $this->hydrationMethod,
            'identity'        => $this->identity,
        ];
    }
    public function getHydrationMethod() {
        return $this->hydrationMethod;
    }
    public function getIdentity() {
        return $this->identity;
    }
}