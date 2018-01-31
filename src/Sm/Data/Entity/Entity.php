<?php


namespace Sm\Data\Entity;


use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Model\ModelDataManager;
use Sm\Data\Property\PropertyContainer;

/**
 * Class Entity
 *
 * Sort of a wrapper class for Models that have an identity we can verify. Bound to a ModelDataManager.
 *
 * @property PropertyContainer $properties
 */
abstract class Entity implements \JsonSerializable {
    /** @var  \Sm\Data\Model\ModelDataManager $modelDataManager */
    protected $modelDataManager;
    
    public function __construct(ModelDataManager $modelDataManager) {
        $this->setModelDataManager($modelDataManager);
    }
    public static function init(ModelDataManager $modelDataManager) {
        return new static($modelDataManager);
    }
    public function __get($name) {
        switch ($name) {
            case 'properties':
                return $this->getProperties();
        }
        return null;
    }
    public function set($name, $value = null) {
        if (is_array($name) && isset($value)) {
            throw new UnimplementedError("Not sure what to do with a name and value");
        }
        
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->set($key, $val);
            }
        } else {
            $this->properties->$name = $value;
        }
        return $this;
    }
    public function getProperties(): PropertyContainer {
        return $this->properties = $this->properties ?? PropertyContainer::init();
    }
    
    /**
     * Find an Entity
     *
     * @param array      $attributes
     * @param int|string $context What of this Entity we should find.
     *
     * @return mixed
     */
    abstract public function find($attributes = [], $context = 0);
    
    /**
     * Save the Entity
     *
     * @param array $attributes The properties that we want to se on this Entity
     *
     * @return mixed
     */
    abstract public function save($attributes = []);
    abstract public function destroy();
    
    protected function setModelDataManager(ModelDataManager $modelDataManager) {
        $this->modelDataManager = $modelDataManager;
        return $this;
    }
    
    public function jsonSerialize() {
        return [
            'properties' => $this->getProperties()->getAll(),
        ];
    }
}