<?php


namespace Sm\Data\Model;


use Sm\Core\Container\Container;
use Sm\Core\Exception\InvalidArgumentException;

class ModelContainer extends Container {
    
    protected $expectedSmID;
    
    public function register($name = null, $registrand = null) {
        if (!is_array($name)) {
            if (!($registrand instanceof Model)) throw new InvalidArgumentException('Can only register Models in ModelContainers');
        }
        return parent::register($name, $registrand); // TODO: Change the autogenerated stub
    }
    public function expectSmID(string $smID): void {
        $this->expectedSmID = $smID;
    }
    
}