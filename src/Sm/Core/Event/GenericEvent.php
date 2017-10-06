<?php


namespace Sm\Core\Event;

/**
 * Class GenericEvent
 * Represents an event that only contains some details (any type) and an event name
 */
final class GenericEvent extends Event {
    protected $event_details;
    protected $event_name;
    public function __construct(string $event_name, $event_details = null) {
        parent::__construct();
        $this->event_name    = $event_name;
        $this->event_details = $event_details;
    }
    function jsonSerialize() {
        return array_merge(parent::jsonSerialize(),
                           [
                               'name'    => $this->event_name,
                               'details' => $this->event_details,
                           ]);
    }
    
}