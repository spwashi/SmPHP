<?php


namespace Sm\Data\Model;


use Sm\Core\Exception\Exception;

/**
 * Class ModelNotFoundException
 *
 * Thrown when looking for a Model we cannot find
 */
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
    
}