<?php


namespace Sm\Data\Model;

/**
 * Interface ModelPersistenceManager
 *
 * An object responsible for persisting or destroying Models
 */
interface ModelPersistenceManager {
    public function save(Model $model);
    public function find(Model $model);
    public function create(Model $modelSchema);
    public function mark_delete(Model $model);
}