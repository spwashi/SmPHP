<?php


namespace Sm\Data\Model;

use Sm\Data\Source\DataSource;

/**
 * Interface ModelPersistenceManager
 *
 * An object responsible for persisting or destroying Models
 */
interface ModelPersistenceManager {
    public function save(Model $model);
    public function find(Model $model);
    public function create(Model $modelSchema);
    public function markDelete(Model $model);
    public function getModelSource($model): DataSource;
}