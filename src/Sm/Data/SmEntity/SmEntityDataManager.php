<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:19 PM
 */

namespace Sm\Data\SmEntity;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Schema\Schema;
use Sm\Core\Schema\SchematicInstantiator;
use Sm\Core\Schema\Schematicized;
use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Core\SmEntity\SmEntityManager;
use Sm\Core\SmEntity\SmEntitySchematic;
use Sm\Data\DataLayer;
use Sm\Logging\Logger;
use Sm\Logging\LoggerAware;
use Sm\Logging\LoggingLayer;

/**
 * Class SmEntityDataManager
 *
 * Handles the loading/configuration of SmEntities w/r to the Data Layer
 *
 */
abstract class SmEntityDataManager implements SmEntityManager, SchematicInstantiator, LoggerAware {
	/** @var null The name of this class/object as it exists as an identity manager */
	protected static $identityManagerName = null;

	/** @var SmEntitySchematic[] $registeredSchematics An array of the Schematics we'll use to initialize instances of this class */
	protected $registeredSchematics = [];
	/** @var \Sm\Data\DataLayer */
	private $dataLayer;
	/** @var SmEntityFactory */
	private $smEntityFactory;
	/** @var Logger $logger */
	private $logger;


	#
	##   Constructors/Initialization
	public function __construct(DataLayer $dataLayer = null, SmEntityFactory $smEntityFactory = null) {
		$this->dataLayer = $dataLayer;
		if (isset($this->smEntityFactory)) $this->setSmEntityFactory($smEntityFactory);
		else $this->getSmEntityFactory();
	}
	/**
	 * Static constructor for SmEntityManagers
	 *
	 * @param \Sm\Data\DataLayer|null $dataLayer
	 * @param SmEntityFactory|null    $smEntityFactory
	 *
	 * @return static
	 */
	public static function init(DataLayer $dataLayer = null, SmEntityFactory $smEntityFactory = null) {
		return new static(...func_get_args());
	}
	/**
	 * Initialize the default SmEntityFactory for this class
	 *
	 * @return mixed
	 */
	abstract protected function createSmEntityFactory(): SmEntityFactory;
	public function setLogger(Logger $logger) {
		$this->logger = $logger;
		return $this;
	}
	public function log($content, $name, $level = null) {
		if ($this->logger instanceof LoggingLayer) {
			return $this->logger->log($content, $name, $level);
		}
		return $this->logger->log($content, $name);
	}
	#
	##  Configuration/
	/**
	 * @param null $schematic
	 *
	 * @return mixed|null
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 */
	public function instantiate($schematic = null) {
		if (is_string($schematic)) {
			$schematic = $this->getSchematicByName($schematic);
		}

		if ($schematic && !($schematic instanceof Schema)) {
			throw new InvalidArgumentException("Can only use Schematics to initialize DataManagers");
		}
		$item = $this->smEntityFactory->resolve(null, $schematic);
		if ($item instanceof Schematicized) {
			return $item->fromSchematic($schematic);
		}

		return $item;
	}
	#
	##  Getters/Setters
	/**
	 * The Factory that helps us create instances of our SmEntities
	 *
	 * @param SmEntityFactory $smEntityFactory
	 *
	 * @return SmEntityDataManager
	 */
	public function setSmEntityFactory(SmEntityFactory $smEntityFactory): SmEntityDataManager {
		$this->smEntityFactory = $smEntityFactory;
		return $this;
	}
	public function getSmEntityFactory(): SmEntityFactory {
		return $this->smEntityFactory = $this->smEntityFactory ?? $this->createSmEntityFactory();
	}
	/**
	 * Register a classname to instantiate based on the SmID provided
	 *
	 * @param callable(string $type=null) $resolver
	 */
	public function registerResolver(callable $resolver) {
		$this->getSmEntityFactory()
		     ->register(null,
			     function ($type = null, $schematic = null) use ($resolver) {
				     if (!($schematic instanceof SmEntitySchematic)) {
					     return null;
				     }

				     return $resolver($schematic->getSmID(), $schematic);
			     });
	}
	/**
	 * @return \Sm\Core\SmEntity\SmEntitySchematic[]
	 */
	public function getRegisteredSchematics(): array {
		return $this->registeredSchematics;
	}
	/**
	 * @param $schematic_name
	 *
	 * @return \Sm\Core\SmEntity\SmEntitySchematic
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 * @throws \Sm\Core\Exception\UnimplementedError
	 */
	public function getSchematicByName(string $schematic_name): SmEntitySchematic {
		if (isset($this->registeredSchematics[$schematic_name])) {
			return $this->registeredSchematics[$schematic_name] ?? null;
		}

		if (!isset(static::$identityManagerName)) {
			throw new UnimplementedError("Can't find schematic without a named identityManager");
		}

		$identityManager_id = '[' . static::$identityManagerName . ']';

		# if `name` was provided, search for `[Model]name`
		if (strpos($schematic_name, $identityManager_id) === 0) {
			throw new InvalidArgumentException("Cannot find Model to match '{$schematic_name}'");
		}

		$new_schematic_name = $identityManager_id . $schematic_name;
		return $this->getSchematicByName($new_schematic_name);
	}
	/**
	 * Save an SmEntitySchematic under a certain SmID
	 *
	 * @param        $smEntitySchematic
	 * @param string $smID
	 *
	 * @return \Sm\Core\SmEntity\SmEntitySchematic
	 */
	protected function registerSchematic(SmEntitySchematic $smEntitySchematic, $smID = null): SmEntitySchematic {
		$smID                              = $smID ?? $smEntitySchematic->getSmID();
		$this->registeredSchematics[$smID] = $smEntitySchematic;
		return $smEntitySchematic;
	}
	public function configure($configuration) {
		$item                = $this->createSchematic()->load($configuration);
		$configuredSchematic = $this->registerSchematic($item);
		return $configuredSchematic;
	}
	/**
	 * Create a schematic of the type that this DataManager manages
	 *
	 * @return \Sm\Core\SmEntity\SmEntitySchematic
	 */
	abstract protected function createSchematic(): SmEntitySchematic;
	public static function parseSmID($smID) {
		if (!is_string($smID)) return false;
		if ($smID[0] !== '[') return false;

		preg_match('/\[(?P<manager>[a-zA-Z_]+)\](?:\{(?P<owner>[\[a-zA-Z_]+\][a-zA-Z_]+)\})?\s?(?P<name>[a-zA-Z_]+)/',
		           $smID,
		           $matches);

		$matches = $matches ? $matches : [];

		return array_filter([
			                    'manager'  => $matches['manager'] ?? null,
			                    'owner'    => $matches['owner'] ?? null,
			                    'name'     => $matches['name'] ?? null,
			                    'identity' => $matches['identity'] ?? null,
		                    ]);
	}
	public function __debugInfo() {
		return ['schematics' => $this->registeredSchematics];
	}
}