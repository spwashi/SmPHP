<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 12:19 PM
 */

namespace Sm\Data\SmEntity;


use Sm\Core\SmEntity\SmEntityFactory;
use Sm\Core\SmEntity\SmEntityManager;
use Sm\Data\DataLayer;

/**
 * Class SmEntityDataManager
 *
 * Handles the loading/configuration of SmEntities w/r to the Data Layer
 *
 */
abstract class SmEntityDataManager implements SmEntityManager {
    /** @var \Sm\Data\DataLayer */
    private $dataLayer;
    /** @var SmEntityFactory */
    private $smEntityFactory;
    
    ############################
    #   Constructors/Initialization
    ############################
    public function __construct(DataLayer $dataLayer = null, SmEntityFactory $smEntityFactory = null) {
        $this->dataLayer = $dataLayer;
        $this->setSmEntityFactory($smEntityFactory);
    }
    
    /**
     * Static constructor for SmEntityManagers
     *
     * @param \Sm\Data\DataLayer|null                $dataLayer
     * @param \Sm\Core\SmEntity\SmEntityFactory|null $smEntityFactory
     *
     * @return static
     */
    public static function init(DataLayer $dataLayer = null, SmEntityFactory $smEntityFactory = null) {
        return new static(...func_get_args());
    }
    public function load($identity) {
        return $this->smEntityFactory->resolve($identity);
    }
    
    /**
     * @param SmEntityFactory $smEntityFactory
     *
     * @return SmEntityDataManager
     */
    public function setSmEntityFactory(SmEntityFactory $smEntityFactory = null): SmEntityDataManager {
        if (!isset($smEntityFactory)) {
            if (isset($this->smEntityFactory)) return $this;
            $smEntityFactory = $this->initializeDefaultSmEntityFactory();
        }
        $this->smEntityFactory = $smEntityFactory;
        return $this;
    }
    /**
     * Initialize the default SmEntityFactory for this class
     *
     * @return mixed
     */
    abstract public function initializeDefaultSmEntityFactory(): SmEntityFactory;
}