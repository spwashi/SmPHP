<?php


namespace Sm\Data\Type;


use Sm\Core\SmEntity\SmEntityFactory;

class DatatypeFactory extends SmEntityFactory {
    public function __construct() {
        parent::__construct();
        $this->register([
                            'int'       => Integer_::class,
                            'undefined' => Undefined_::class,
                            'string'    => String_::class,
                            'null'      => Null_::class,
                            'datetime'  => DateTime_::class,
                        ]);
        
    }
    public function build($name = null, $schematic = null) {
        $datatype_sm_id = '[Datatype]';
        # todo test
        if (strpos($name, $datatype_sm_id) !== false) $name = substr($name, strlen($datatype_sm_id));
        return parent::build($name, $schematic);
    }
    
    
    protected function canCreateClass($object_type) {
        return true; # Datatypes are weird... not sure about this
    }
    
}