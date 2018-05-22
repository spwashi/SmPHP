<?php


namespace Sm\Data\Model\Exception\Persistence;


use Sm\Data\Model\Exception\ModelPersistenceException;

class CannotCreateModelException extends ModelPersistenceException {
    protected $message           = 'Could not save model';
    protected $failed_properties = [];
    public function setFailedProperties(array $properties = []) {
        $this->failed_properties = $properties;
        return $this;
    }
    public function jsonSerialize() {
        return parent::jsonSerialize() +
               [
                   'failed_properties' => $this->failed_properties,
               ];
    }
}