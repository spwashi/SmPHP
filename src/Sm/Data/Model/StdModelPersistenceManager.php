<?php


namespace Sm\Data\Model;


use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Model\Exception\ModelNotFoundException;
use Sm\Data\Model\Exception\ModelNotSoughtException;
use Sm\Data\Type\Undefined_;
use Sm\Query\Interpretation\QueryInterpreter;
use Sm\Query\Statements\DeleteStatement;
use Sm\Query\Statements\InsertStatement;
use Sm\Query\Statements\SelectStatement;
use Sm\Query\Statements\UpdateStatement;

class StdModelPersistenceManager implements ModelPersistenceManager {
    const MONITOR__QUERY_EXECUTED = 'QUERY__EXECUTED';
    use HasMonitorTrait;
    /** @var  QueryInterpreter $queryInterpreter */
    protected $queryInterpreter;
    
    
    public function setQueryInterpreter(QueryInterpreter $queryInterpreter) {
        $this->queryInterpreter = $queryInterpreter;
        
        return $this;
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
    
    /**
     * Locate one
     *
     * @param \Sm\Data\Model\Model $model
     *
     * @return \Sm\Data\Model\Model
     * @throws \Sm\Data\Model\Exception\ModelNotFoundException
     */
    public function find(Model $model): Model {
        $result = $this->selectFind($model);
        $model->set($result[0] ?? []);
        return $model;
    }
    /**
     * Find multiple models that math this one
     *
     * @param \Sm\Data\Model\Model $model
     *
     * @return \Sm\Data\Model\Model[]
     */
    public function findAll(Model $model) {
        $result      = $this->selectFind($model);
        $end_results = [];
        foreach ($result as $item) {
            $end_results[] = (clone $model)->set($item);
        }
        return $end_results;
    }
    public function save(Model $model) {
        $properties = $model->getChanged();
        $update     = UpdateStatement::init($properties)
                                     ->inSources($model->getName())
                                     ->where(EqualToCondition::init($model->properties->id,
                                                                    $model->properties->id->value));
        #$result1 = $this->queryInterpreter->getQueryFormatter()->format($update);
        return $this->queryInterpreter->interpret($update);
    }
    public function create(Model $model) {
        $properties = $model->getProperties()->getAll();
        $insert     = InsertStatement::init($this->getModelNon_IdentityProperties($model, $properties))
                                     ->inSources($model->getName());
        #$result1 = $this->queryInterpreter->getQueryFormatter()->format($insert);
        return $this->queryInterpreter->interpret($insert);
    }
    public function mark_delete(Model $model) {
        $update = UpdateStatement::init([ $model->properties->delete_dt->getName() => date("Y-m-d H:i:s") ])
                                 ->inSources($model->getName())
                                 ->where(EqualToCondition::init($model->properties->id,
                                                                $model->properties->id->value));
        #$result1 = $this->queryInterpreter->getQueryFormatter()->format($update);
        return $this->queryInterpreter->interpret($update);
    }
    public function delete(Model $model) {
        $update  = DeleteStatement::init()
                                  ->from($model->getName())
                                  ->where(EqualToCondition::init($model->properties->id,
                                                                 $model->properties->id->value));
        $result1 = $this->queryInterpreter->getQueryFormatter()->format($update);
        return $this->queryInterpreter->interpret($update);
    }
    /**
     * @param \Sm\Data\Model\Model $model
     *
     * @return array
     * @throws \Sm\Data\Model\Exception\ModelNotFoundException
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Resolvable\Error\UnresolvableException
     */
    protected function selectFind(Model $model) {
        $properties = $model->properties;
        $conditions = [];
        
        /** @var \Sm\Data\Property\Property $property */
        foreach ($properties as $property_name => $property) {
            if (!($property->raw_value instanceof Undefined_)) {
                $conditions[] = EqualToCondition::init($property);
            }
        }
        
        if (empty($conditions)) {
            throw (new ModelNotSoughtException("Cannot search without conditions"))->setModel($model);
            
        }
        
        $select       = SelectStatement::init()
                                       ->select(...array_keys($properties->getAll()))
                                       ->from($model->getName())
                                       ->where(...$conditions);
        $selectResult = $this->queryInterpreter->interpret($select);
        
        if (empty($selectResult)) {
            throw (new ModelNotFoundException("Could not find Model"))->setModel($model)
                                                                      ->addMonitors(array_merge_recursive($this->getMonitorContainer()->getAll(),
                                                                                                          $this->queryInterpreter->getMonitorContainer()->getAll()))
                                                                      ->setModelSearchConditions($conditions);
        }
        return $selectResult;
    }
}