<?php


namespace Sm\Data\Model;


use Sm\Core\Container\Container;
use Sm\Core\Exception\InvalidArgumentException;

class ModelContainer extends Container {
    
    protected $expectedSmID;
    
    public function register($name = null, $registrant = null) {
        if (!is_array($name)) {
            if (!($registrant instanceof Model)) throw new InvalidArgumentException('Can only register Models in ModelContainers');
        }
        return parent::register($name, $registrant);
    }
    public function expectSmID(string $smID): void {
        $this->expectedSmID = $smID;
    }
    
}