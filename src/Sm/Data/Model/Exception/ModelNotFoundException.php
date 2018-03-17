<?php


namespace Sm\Data\Model\Exception;


use Sm\Core\Exception\Exception;

class ModelNotFoundException extends Exception {
    protected $model;
    protected $model_search_conditions;
    public function setModel($model) {
        $this->model = $model;
        return $this;
    }
    public function setModelSearchConditions($model_search_conditions) {
        $this->model_search_conditions = $model_search_conditions;
        return $this;
    }
    public function jsonSerialize() {
        return [
                   'model'      => $this->model,
                   'conditions' => $this->model_search_conditions,
               ] + parent::jsonSerialize();
    }
    public function getModelSearchConditions() {
        return $this->model_search_conditions;
    }
    public function getModel() {
        return $this->model;
    }
    
}