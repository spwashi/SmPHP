<?php


namespace Sm\Core\Internal\Monitor;

/**
 * Class Monitor
 *
 * Just meant to keep track of events in an undefined way
 */
class Monitor {
    const STD  = '.';
    const NOTE = 'note';
    protected $events = [];
    /**
     * Make note that an event happened
     *
     * @param        $event_name
     * @param array  ...$event_details
     *
     * @return $this
     */
    public function appendEvent($event_name, ...$event_details) {
        $this->events[] = [
            'event'   => $event_name,
            'details' => empty($event_details) ? null : (count($event_details) === 1 ? $event_details[0] : $event_details),
        ];
        return $this;
    }
    /**
     * Get the events that have been noted by this class.
     *
     * @param string|null $event_type The type of event type that we want to get the information about
     *
     * @return array|null
     */
    public function getEvents(string $event_type = null): ? array {
        if (isset($event_type)) return $this->events[ $event_type ] ?? null;
        return $this->events;
    }
    /**
     * Clear the event information that we've been holding at a specified index or in general
     *
     * @param string|null $event_type
     *
     * @return $this
     */
    public function clear(string $event_type = null) {
        if (isset($event_type)) $this->events[ $event_type ] = [];
        else $this->events = [];
        return $this;
    }
}