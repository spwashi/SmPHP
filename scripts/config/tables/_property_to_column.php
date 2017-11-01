<?php

use Sm\Data\Property\PropertySchematic;
use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;
use Sm\Query\Modules\Sql\Data\Column\IntegerColumnSchema;
use Sm\Query\Modules\Sql\Data\Column\VarcharColumnSchema;

function propererty_to_column(PropertySchematic $propertySchema) {
    $datatypes = $propertySchema->getRawDataTypes();
    
    
    $first_datatype = $datatypes[0] ?? null;
    
    if (strpos($first_datatype, '[Datatype]') !== false) {
        $first_datatype = substr($first_datatype, strlen('[Datatype]'));
    }
    
    
    switch ($first_datatype) {
        case 'int':
            $column = _initIntColumn($propertySchema);
            break;
        case 'string':
            $column = _initStringColumn($propertySchema);
            break;
        default :
            $column = $first_datatype;
    }
    
    if (isset($column)) {
        $is_null = in_array('[Datatype]null', $datatypes);
        $column->setNullability($is_null);
    }
    
    
    return $column;
}

function _initIntColumn(PropertySchematic $propertySchema): ColumnSchema {
    $column = IntegerColumnSchema::init()
                                 ->setName($propertySchema->getName())
                                 ->setLength($propertySchema->getLength());
    return $column;
}

function _initStringColumn(PropertySchematic $propertySchema): ColumnSchema {
    $column = VarcharColumnSchema::init()
                                 ->setName($propertySchema->getName())
                                 ->setLength($propertySchema->getLength() ?? 25);
    return $column;
}