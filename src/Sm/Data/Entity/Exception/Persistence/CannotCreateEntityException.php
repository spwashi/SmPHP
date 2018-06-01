<?php


namespace Sm\Data\Entity\Exception\Persistence;


use Sm\Data\Entity\Exception\EntityPersistenceException;

class CannotCreateEntityException extends EntityPersistenceException {
    protected $message           = 'Could not save model';
    protected $failed_properties = [];
    public function setFailedProperties(array $properties = []) {
        $this->failed_properties = array_filter($properties);
        return $this;
    }
    public function jsonSerialize() {
        return parent::jsonSerialize() +
               [
                   'failed_properties' => $this->failed_properties,
               ];
    }
    /**
     * @return array
     */
    public function getFailedProperties(): array {
        return $this->failed_properties;
    }
}