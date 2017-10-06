<?php


namespace Sm\Data\Property\Event;


use Sm\Core\Event\Event;

/**
 * Class PropertyValueChange
 *
 * Event that is dispatched when the value of a property is changed
 */
class PropertyValueChange extends Event {
    protected $property;
    protected $updated_value;
    protected $previous_value;
    /**
     * PropertyValueChange constructor.
     *
     * @param $property
     * @param $new_value
     */
    public function __construct($property, $new_value) {
        $this->property      = $property;
        $this->updated_value = $new_value;
        parent::__construct();
    }
    public static function init($property, $new_value) {
        return new static($property, $new_value);
    }
    function jsonSerialize() {
        return array_merge(parent::jsonSerialize(),
                           [
                               'property_object_id' => $this->property->getObjectId(),
                               'value'              => $this->updated_value,
                           ]);
    }
    
}