<?php


namespace Sm\Data\Type;


use Sm\Core\SmEntity\SmEntityFactory;

class DatatypeFactory extends SmEntityFactory {
    public function __construct() {
        parent::__construct();
        $this->register([
                            'int'      => Integer_::class,
                            'string'   => String_::class,
                            'null'     => Null_::class,
                            'datetime' => DateTime_::class,
                        ]);
        
    }
    protected function canCreateClass($object_type) {
        return true; # Datatypes are weird... not sure about this
    }
    
}