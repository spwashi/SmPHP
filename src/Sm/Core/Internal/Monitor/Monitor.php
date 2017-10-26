<?php


namespace Sm\Core\Internal\Monitor;

use Sm\Core\Event\Event;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;

/**
 * Class Monitor
 *
 * Just meant to keep track of events in an undefined way
 */
class Monitor implements \JsonSerializable, Identifiable {
    const INFO = 'note';
    #
    /** @var Event[] */
    protected $events      = [];
    protected $date_format = DATE_ATOM;
    use HasObjectIdentityTrait;
    public function __construct() {
        $this->createSelfID();
    }
    
    
    /**
     * Make note that an event happened
     *
     * @param \Sm\Core\Event\Event $event
     *
     * @return $this
     * @internal param $event_name
     * @internal param mixed $event_details
     *
     * @internal param null|\Sm\Core\Internal\Identification\Identifiable $source
     *
     */
    public function append(Event $event) {
        $this->events[] = $event;
        return $this;
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
    function jsonSerialize() {
        return [
            'object_id' => $this->getObjectId(),
            'events'    => $this->events,
        ];
    }
    /**
     * @return \Sm\Core\Event\Event[]
     */
    public function dump(): array {
        return $this->events;
    }
}