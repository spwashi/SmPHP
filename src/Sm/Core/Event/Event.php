<?php


namespace Sm\Core\Event;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;


abstract class Event implements Identifiable, \JsonSerializable {
    use HasObjectIdentityTrait;
    protected $creation_timestamp;
    protected $date_format = DATE_ATOM;
    
    public function __construct() {
        $this->createSelfID();
        $this->creation_timestamp = $this->createTimestamp();
    }
    
    protected function createTimestamp() {
        return date($this->date_format);
    }
    
    function jsonSerialize() {
        return [
            'event'           => static::class,
            'event_object_id' => $this->getObjectId(),
            'creation_ts'     => $this->creation_timestamp,
        ];
    }
}