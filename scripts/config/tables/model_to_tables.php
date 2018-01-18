<?php

use Sm\Modules\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Modules\Sql\Constraints\UniqueKeyConstraintSchema;
use Sm\Modules\Sql\MySql\Module\MySqlQueryModule;
use Sm\Modules\Sql\Statements\CreateTableStatement;

require_once '_property_to_column.php';

/** @var \Sm\Application\Application $app */

/** @var \Sm\Data\Model\ModelSchematic[] $models */
$models = $app->data->models->getConfiguredModels();

$all                       = [];
$queries                   = [];
$_allColumns__propertySmID = [];
$uniqueKeyConstraints      = [];
foreach ($models as $modelSmID => $model) {
    $properties = $model->getProperties();
    $meta       = $model->getPropertyMeta();
    $columns    = [];
    $primaries  = [];
    /**
     * @var                                     $propertySmID
     * @var \Sm\Data\Property\PropertySchematic $property
     */
    foreach ($properties as $propertySmID => $property) {
        $column = propererty_to_column($property);
        if (!$column) continue;
        if ($meta->isPrimary($property)) {
            $primaries[] = $column;
        }
        $columns[]                                         = $column;
        $_allColumns__propertySmID[ $property->getSmID() ] = $column;
    }
    
    /** @var CreateTableStatement $createTable */
    $createTable = CreateTableStatement::init($model->getName())
                                       ->withColumns(...$columns);
    
    if (count($primaries)) {
        $createTable->withConstraints(PrimaryKeyConstraintSchema::init()
                                                                ->addColumn(...$primaries));
    }
    
    # figure out unique keys
    $_unique_keys = $meta->getUniqueKeyGroup();
    if (!empty($_unique_keys)) {
        $constraint = UniqueKeyConstraintSchema::init();
        foreach ($_unique_keys as $_uniquePropertySmID) {
            $column = $_allColumns__propertySmID[ $_uniquePropertySmID ] ?? null;
            
            if (!isset($column)) {
                throw new Error("Could not find column {$_uniquePropertySmID}");
            }
            $constraint->addColumn($column);
        }
        $createTable->withConstraints($constraint);
    }
    
    $queries[] = $createTable;
    $all[]     = MySqlQueryModule::init()->initialize()->getQueryFormatter()->format($createTable);
}


$do_interpret = 1;

if ($do_interpret == true) {
    foreach ($queries as $query) {
        
        echo "<pre>";
        echo MySqlQueryModule::init()->initialize()->getQueryFormatter()->format($query);
        echo "</pre><br>";
        
        $app->query->interpret($query);
    }
}


$joined = join('<br>', $all);
echo "<pre>{$joined}</pre>";