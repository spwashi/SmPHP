<?php


namespace Sm\Data\Property\Event;


use Sm\Core\Event\Event;

/**
 * Class PropertyValueChange
 *
 * Event that is dispatched when the value of a property is changed
 */
class PropertyValueChange extends Event {
    /** @var  \Sm\Data\Property\Property $property */
    protected $property;
    protected $updated_value;
    protected $previous_value;
    /**
     * PropertyValueChange constructor.
     *
     * @param      $property
     * @param      $new_value
     * @param null $previous_value
     */
    public function __construct($property, $new_value, $previous_value = null) {
        $this->property       = $property;
        $this->updated_value  = $new_value;
        $this->previous_value = $previous_value;
        parent::__construct();
    }
    public static function init($property, $new_value, $previous_value = null) {
        return new static(...func_get_args());
    }
    function jsonSerialize() {
        return array_merge(parent::jsonSerialize(),
                           [
                               'property_object_id' => $this->property->getObjectId(),
                               'value'              => $this->updated_value,
                           ],
                           isset($this->previous_value) ? [ 'previous' => $this->previous_value ] : []);
    }
    
}