<?php


namespace Sm\Data\Model;


use Sm\Core\SmEntity\SmEntity;
use Sm\Data\Model\Resolvable\RawModelPropertyResolvable;
use ICanBoogie\Inflector;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Resolvable\Exception\UnresolvableException;
use Sm\Core\Resolvable\NativeResolvable;
use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Model\Context\ModelCreationContext;
use Sm\Data\Model\Exception\ModelLayerConfigurationError;
use Sm\Data\Model\Exception\ModelNotFoundException;
use Sm\Data\Model\Exception\ModelNotSoughtException;
use Sm\Data\Model\Exception\Persistence\CannotCreateModelException;
use Sm\Data\Property\PropertySchemaContainer;
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

	#
	## Constructors
	public function __construct(ModelFactory $modelFactory = null) {
		$this->modelFactory = $modelFactory ?? ModelFactory::init();
	}

	#
	## Getters and Setters
	public function setQueryInterpreter(QueryInterpreter $queryInterpreter) {
		$this->queryInterpreter = $queryInterpreter;

		return $this;
	}
	public function setModelFactory(ModelFactory $modelFactory) {
		$this->modelFactory = $modelFactory;
		return $this;
	}

	#
	## Fetching
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
	 * @throws \Sm\Data\Model\Exception\ModelNotSoughtException
	 */
	public function find(ModelSchema $search, $do_hydrate = false): ModelSchema {
		$result = $this->selectFind($search);

		return $this->hydrateModel($search,
		                           $this->normalizeFoundSet($result[0] ?? []));
	}
	public function setFindSafety($do_safe_finds = true) {
		$this->do_safe_finds = $do_safe_finds;
		return $this;
	}
	/**
	 * Find multiple models that match this one
	 *
	 * @param ModelSchema $model
	 *
	 * @return Model[]
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 * @throws \Sm\Data\Model\Exception\ModelNotSoughtException
	 * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
	 */
	public function findAll(ModelSchema $model) {
		$result      = $this->selectFind($model);
		$end_results = [];

		foreach ($result as $item) {
			$model         = clone $model;
			$end_results[] = $this->hydrateModel($model, $item);
		}
		return $end_results;
	}
	/**
	 * Get the result of a Select query searching for a Model
	 *
	 * @param \Sm\Data\Model\ModelSchema $model
	 *
	 * @return array
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Core\Resolvable\Exception\UnresolvableException
	 * @throws \Sm\Data\Model\Exception\ModelNotSoughtException
	 */
	protected function selectFind(ModelSchema $model) {
		/** @var \Sm\Data\Property\PropertySchemaContainer $properties */
		$properties = $model->getProperties();
		$conditions = [];

		/** @var \Sm\Data\Property\Property $property */
		foreach ($properties as $property_name => $property) {
			if (!($property instanceof SmEntity)) continue;
			if (!($property->raw_value instanceof Undefined_)) {
				$conditions[] = EqualToCondition::init($property);
			}
		}

		if (empty($conditions) && $this->do_safe_finds) {
			$modelNotSoughtException = new ModelNotSoughtException("Cannot search without conditions");
			$modelNotSoughtException->setModel($model);
			throw $modelNotSoughtException;

		}

		$all_properties_array = $properties->getAll(true);
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

	#
	## Saving
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

	#
	## Creation
	/**
	 * Create the Model in whichever source maintains its identity
	 *
	 * @param Model $model
	 *
	 * @return mixed
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
	 * @throws \Sm\Data\Model\Exception\Persistence\CannotCreateModelException
	 */
	public function create(Model $model) {
		// Establish the Context for validating the properties of this Model
		$creationContext = new ModelCreationContext;

		$validationResult = $model->validate($creationContext);

		// Models always return a ValidationResult at the moment
		//     But the reason there would not be one is if the validation has been successful (as is the case with Properties)
		//      If there have been errors in the validation, we want to throw an exception before creating the Model
		if (isset($validationResult) && !$validationResult->isSuccess()) {

			$cannotCreateModelException = new CannotCreateModelException;
			$propertyValidationResults  = $validationResult->getPropertyValidationResults();
			$cannotCreateModelException->setFailedProperties($propertyValidationResults);

			throw $cannotCreateModelException;
		}

		$table_name = $this->modelToTablename($model);

		// We insert the properties that are not related to this Model's Identity (because the database is responsible for generating those properties)
		//      todo is this a safe assumption? For now...
		$non_IdentityProperties = $this->getModelNon_IdentityProperties($model, $model->getProperties());
		$insert                 = InsertStatement::init($non_IdentityProperties)
		                                         ->inSources($table_name);

		// The MySQL Query Interpreter returns an ID of the newly created model for INSERT statements
		$id = $this->queryInterpreter->interpret($insert);

		$model->set('id', $id);

		// We've set properties on this Model, but we want future modifications of the Model Properties to designate changes (not the creation)
		$model->markUnchanged();

		return $model;
	}

	#
	## Deletion
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
		$update                  = UpdateStatement::init([$delete_dt_property_name => date("Y-m-d H:i:s")])
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

	#
	## Meta
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
	 * @param \Sm\Data\Model\ModelSchema $model
	 * @param                            $properties
	 *
	 * @return $this|mixed|null|\Sm\Data\Model\Model
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Data\Property\Exception\ReadonlyPropertyException
	 * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
	 */
	public function hydrateModel(ModelSchema $model, array $properties) {
		$fetched_data = $properties;
		$fetched_data = $this->normalizeFoundSet($fetched_data);

		if ($model instanceof Model) {
			# Return the model with synchronized values
			return $model->set($fetched_data);
		}


		# We don't want to mutate the schematic, so we clone it

		/** @var \Sm\Data\Model\ModelSchema $schematic */
		$schematic = clone $model;


		try {

			$smID = $schematic->getSmID();
			$item = $this->modelFactory->resolve($smID ?? null, $schematic);
			$item->fromSchematic($schematic);
			$item->set($fetched_data);

			return $item;
		} catch (UnresolvableException $exception) {
			$error = (new ModelLayerConfigurationError("Could not resolve from"))->setModel($schematic);
			throw $error;
		}
	}

	/**
	 * Get the properties that would be set on creation (not related to the Identity of the Model)
	 *
	 * @param \Sm\Data\Model\ModelSchema                     $model
	 * @param \Sm\Data\Property\PropertySchemaContainer|null $properties
	 *
	 * @return \Sm\Data\Property\PropertySchemaContainer
	 */
	protected function getModelNon_IdentityProperties(ModelSchema $model, PropertySchemaContainer $properties = null) {
		$property_array = $properties->getAll();

		$id_properties  = $this->getModelIdentityProperties($model);
		$property_array = count($property_array) ? $property_array : $model->properties->getAll();
		$array_diff     = array_diff(array_keys($property_array), array_keys($id_properties));

		return $model->getProperties($array_diff);
	}
	protected function getModelIdentityProperties(ModelSchema $model): array {
		$id = $model->getProperties()->id;
		return [$id->getName() => $id];
	}
	protected function getQueryMonitors(): array {
		$these_monitors             = $this->getMonitorContainer()->getAll();
		$query_interpreter_monitors = $this->queryInterpreter->getMonitorContainer()->getAll();
		$monitors                   = array_merge_recursive($these_monitors, $query_interpreter_monitors);
		return $monitors;
	}
	protected function normalizeFoundSet($item) {
		foreach ($item as $index => &$value) {
			$value = RawModelPropertyResolvable::init($value);
		}
		return $item;
	}
}