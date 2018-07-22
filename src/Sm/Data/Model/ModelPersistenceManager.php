<?php


namespace Sm\Data\Model;

use Sm\Core\Context\Context;
use Sm\Data\Source\DataSource;

/**
 * Interface ModelPersistenceManager
 *
 * An object responsible for persisting or destroying Models
 */
interface ModelPersistenceManager {
	public function save(ModelInstance $model);
	public function find(ModelSchema $schematic);
	public function findAll(ModelSchema $schematic);
	public function create(ModelInstance $modelSchema, Context $context = null);
	public function markDelete(ModelInstance $model);
	public function getModelSource($model): DataSource;
}