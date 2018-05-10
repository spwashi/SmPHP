<?php


namespace Sm\Data\Model\Exception;


use Sm\Core\Exception\Error;

class ModelLayerConfigurationError extends Error {
    protected $modelSchema;
    public function setModel($model) {
        $this->model = $model;
        return $this;
    }
    public function jsonSerialize() {
        return [
                   'model' => $this->model,
               ] + parent::jsonSerialize();
    }
    
}