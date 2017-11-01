<?php

namespace EXAMPLE_APP_NAMESPACE\Controller;

use Error;
use Sm\Application\Controller\BaseApplicationController;
use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Model\Model;
use Sm\Data\Property\PropertySchematic;
use Sm\Query\Modules\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Query\Modules\Sql\Constraints\UniqueKeyConstraintSchema;
use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;
use Sm\Query\Modules\Sql\Data\Column\IntegerColumnSchema;
use Sm\Query\Modules\Sql\Data\Column\VarcharColumnSchema;
use Sm\Query\Modules\Sql\MySql\Module\MySqlQueryModule;
use Sm\Query\Modules\Sql\Statements\CreateTableStatement;

/**
 * Class Home
 *
 * The controller that contains the core of the application logic.
 */
class Dev extends BaseApplicationController {
    
    protected function propertyToColumn(PropertySchematic $propertySchema) {
        $datatypes = $propertySchema->getRawDataTypes();
        
        
        $first_datatype = $datatypes[0] ?? null;
        
        if (strpos($first_datatype, '[Datatype]') !== false) {
            $first_datatype = substr($first_datatype, strlen('[Datatype]'));
        }
        
        
        switch ($first_datatype) {
            case 'int':
                $column = $this->_initIntColumn($propertySchema);
                break;
            case 'string':
                $column = $this->_initStringColumn($propertySchema);
                break;
            default :
                throw new UnimplementedError("Cannot create property for {$first_datatype} yet");
        }
        
        if (isset($column)) {
            $is_null = in_array('[Datatype]null', $datatypes);
            var_dump($column);
            $column->setNullability($is_null);
        }
        
        
        return $column;
    }
    
    protected function _initIntColumn(PropertySchematic $propertySchema): ColumnSchema {
        $column = IntegerColumnSchema::init()
                                     ->setName($propertySchema->getName())
                                     ->setLength($propertySchema->getLength());
        return $column;
    }
    
    protected function _initStringColumn(PropertySchematic $propertySchema): ColumnSchema {
        $column = VarcharColumnSchema::init()
                                     ->setName($propertySchema->getName())
                                     ->setLength($propertySchema->getLength() ?? 25);
        return $column;
    }
    
    public function modelsToTables() {
        $app = $this->app;
        
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
                $column = $this->propertyToColumn($property);
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
    }
    
    public function eg() {
        $application         = $this->app;
        $representationLayer = $application->representation;
        $dataLayer           = $application->data;
        
        $model_manager = $dataLayer->getDataManager(Model::class);
        
        # -- rendering
        
        $vars     = [ 'path_to_site' => $this->app->path, ];
        $rendered = $representationLayer->render('hello.twig', $vars);
        
        #
        
        return $rendered;
    }
}