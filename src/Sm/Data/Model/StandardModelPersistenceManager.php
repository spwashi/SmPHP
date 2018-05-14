<?php


namespace Sm\Data\Model;


use ICanBoogie\Inflector;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Resolvable\Exception\UnresolvableException;
use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Model\Exception\ModelLayerConfigurationError;
use Sm\Data\Model\Exception\ModelNotFoundException;
use Sm\Data\Model\Exception\ModelNotSoughtException;
use Sm\Data\Source\Database\Table\TableSource;
use Sm\Data\Source\DataSource;
use Sm\Data\Type\Undefined_;
use Sm\Query\Interpretation\QueryInterpreter;
use Sm\Query\Statements\DeleteStatement;
use Sm\Query\Statements\InsertStatement;
use Sm\Query\Statements\SelectStatement;
use Sm\Query\Statements\UpdateStatement;

class StandardModelPersistenceManager implements ModelPersistenceManager {
    const MONITOR__QUERY_EXECUTED = 'QUERY__EXECUTED';
    use HasMonitorTrait;
    /** @var  QueryInterpreter $queryInterpreter */
    protected $queryInterpreter;
    protected $do_safe_finds = true;
    /** @var \Sm\Core\Container\Container|\Sm\Data\Model\ModelFactory */
    protected $modelFactory;
    public function __construct(ModelFactory $modelFactory = null) {
        $this->modelFactory = $modelFactory ?? ModelFactory::init();
    }
    public function setQueryInterpreter(QueryInterpreter $queryInterpreter) {
        $this->queryInterpreter = $queryInterpreter;
        
        return $this;
    }
    /**
     * Retrieve and instantiate a Model
     *
     * @param \Sm\Data\Model\ModelSchema $search
     *
     * @param bool                       $do_hydrate
     *
     * @return Model
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
     * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
     */
    public function find(ModelSchema $search, $do_hydrate = false): ModelSchema {
        $result = $this->selectFind($search, $do_hydrate);
        /** @var \Sm\Data\Model\ModelSchema $schematic */
        $schematic = (clone $search)->set($result[0] ?? []);
        try {
            $smID = $schematic->getSmID();
            $item = $this->modelFactory->resolve($smID ?? null, $schematic);
            $item->fromSchematic($schematic);
            return $item;
        } catch (UnresolvableException $exception) {
            $error = (new ModelLayerConfigurationError("Could not resolve from"))->setModel($schematic);
            throw $error;
        }
    }
    /**
     * Find multiple models that match this one
     *
     * @param Model $model
     *
     * @return Model[]
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     */
    public function findAll(Model $model) {
        $result      = $this->selectFind($model);
        $end_results = [];
        foreach ($result as $item) {
            $end_results[] = (clone $model)->set($item);
        }
        return $end_results;
    }
    /**
     * Save a Model in whichever source maintains the association of its identity and its properties
     *
     * @param Model $model
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function save(Model $model) {
        $properties = $model->getChanged();
        $table_name = $this->modelToTablename($model);
        $update     = UpdateStatement::init($properties)->inSources($table_name)->where(EqualToCondition::init($model->properties->id,
                                                                                                               $model->properties->id->value));
        #$result1 = $this->queryInterpreter->getQueryFormatter()->format($update);
        return $this->queryInterpreter->interpret($update);
    }
    /**
     * Create the Model in whichever source maintains its identity
     *
     * @param Model $model
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
     */
    public function create(Model $model) {
        $properties             = $model->getProperties()->getAll();
        $table_name             = $this->modelToTablename($model);
        $non_IdentityProperties = $this->getModelNon_IdentityProperties($model, $properties);
        $insert                 = InsertStatement::init($non_IdentityProperties)->inSources($table_name);
        $id                     = $this->queryInterpreter->interpret($insert);
        $model->set('id', $id);
        $model->markUnchanged();
        return $model;
    }
    /**
     * Instead of Deleting something, mark a flag that either queues it for deletion or removes it from the pool of valid models
     *
     * @param Model $model
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function markDelete(Model $model) {
        $delete_dt_property_name = $model->properties->delete_dt->getName();
        $table_name              = $this->modelToTablename($model);
        $update                  = UpdateStatement::init([ $delete_dt_property_name => date("Y-m-d H:i:s") ])
                                                  ->inSources($table_name)
                                                  ->where(EqualToCondition::init($model->properties->id,
                                                                                 $model->properties->id->value));
        #$result1 = $this->queryInterpreter->getQueryFormatter()->format($update);
        return $this->queryInterpreter->interpret($update);
    }
    /**
     * Remove a model from its maintainer-of-identity
     *
     * @param Model $model
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function delete(Model $model) {
        $table_name = $this->modelToTablename($model);
        $delete     = DeleteStatement::init()
                                     ->from($table_name)
                                     ->where(EqualToCondition::init($model->properties->id,
                                                                    $model->properties->id->value));
        #$result1 = $this->queryInterpreter->getQueryFormatter()->format($update);
        return $this->queryInterpreter->interpret($delete);
    }
    /**
     * @param string|\Sm\Data\Model\ModelSchema $model
     *
     * @return string
     */
    public function modelToTablename($model): string {
        $model_name = $model instanceof ModelSchema ? $model->getName() : $model;
        
        if (strpos($model_name, '[Model]') === 0) $model_name = str_replace('[Model]', '', $model_name);
        
        return Inflector::get()->pluralize($model_name);
    }
    /**
     * @param \Sm\Data\Model\ModelSchema|string $model
     *
     * @return \Sm\Data\Source\DataSource
     */
    public function getModelSource($model): DataSource {
        $table_name = $this->modelToTablename($model);
        return TableSource::init()->setTableName($table_name);
    }
    /**
     * Get the result of a Select query searching for a Model
     *
     * @param Model $model
     *
     * @return array
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    protected function selectFind(ModelSchema $model, $do_hydrate = false) {
        $properties = $model->properties;
        $conditions = [];
        
        /** @var \Sm\Data\Property\Property $property */
        foreach ($properties as $property_name => $property) {
            if (!($property->raw_value instanceof Undefined_)) {
                $conditions[] = EqualToCondition::init($property);
            }
        }
        
        if (empty($conditions) && $this->do_safe_finds) {
            throw (new ModelNotSoughtException("Cannot search without conditions"))->setModel($model);
            
        }
        
        $all_properties_array = $properties->getAll();
        $joined_properties    = [];
        /**
         * @var \Sm\Data\Property\Property $_ap_property
         */
        foreach ($all_properties_array as $_ap_propertyName => $_ap_property) {
            $referenceDescriptor = $_ap_property->getReferenceDescriptor();
            if (!isset($referenceDescriptor)) continue;
            
            /** @var string $identity */
            $identity = $referenceDescriptor->getIdentity();
            if (!isset($identity)) continue;
            if (!is_string($identity)) {
                throw new UnimplementedError("Can only hydrate using SmIDs");
            }
        }
        $table_name   = $this->modelToTablename($model);
        $select       = SelectStatement::init()
                                       ->select(...array_keys($all_properties_array))
                                       ->from($table_name)
                                       ->where(...$conditions);
        $selectResult = $this->queryInterpreter->interpret($select);
        
        if (empty($selectResult)) {
            $monitors = $this->getQueryMonitors();
            throw (new ModelNotFoundException("Could not find Model"))->setModel($model)
                                                                      ->addMonitors($monitors)
                                                                      ->setModelSearchConditions($conditions);
        }
        return $selectResult;
    }
    protected function getModelNon_IdentityProperties(ModelSchema $model, array $properties = null) {
        $id_properties = $this->getModelIdentityProperties($model);
        $properties    = count($properties) ? $properties : $model->properties->getAll();
        $array_diff    = array_diff($properties, $id_properties);
        return $array_diff;
        
    }
    protected function getModelIdentityProperties(ModelSchema $model): array {
        $id = $model->getProperties()->id;
        return [ $id->getName() => $id ];
    }
    protected function getQueryMonitors(): array {
        $these_monitors             = $this->getMonitorContainer()->getAll();
        $query_interpreter_monitors = $this->queryInterpreter->getMonitorContainer()->getAll();
        $monitors                   = array_merge_recursive($these_monitors, $query_interpreter_monitors);
        return $monitors;
    }
    public function setFindSafety($do_safe_finds = true) {
        $this->do_safe_finds = $do_safe_finds;
        return $this;
    }
    public function setModelFactory(ModelFactory $modelFactory) {
        $this->modelFactory = $modelFactory;
        return $this;
    }
}