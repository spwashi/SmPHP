<?php

namespace Sm\Data\Model;


use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertySchematic;

class ModelPropertyMetaSchematicTest extends \PHPUnit_Framework_TestCase {
    public function testCanCheckPrimary() {
        $mpms = new ModelPropertyMetaSchematic(new PropertyContainer);
        $smID = '[Property]smID';
        $mpms->load([ 'primary' => [ $smID ] ]);
        $result   = $mpms->isPrimary(PropertySchematic::init()->load([ 'smID' => $smID ]));
        $result_2 = $mpms->isPrimary(PropertySchematic::init()->load([ 'smID' => 'random' ]));
        $this->assertTrue($result);
        $this->assertFalse($result_2);
    }
    
    public function testCanCheckUnique() {
        $mpms = new ModelPropertyMetaSchematic(new PropertyContainer);
        $smID = '[Property]smID';
        $mpms->load([ 'primary' => [ $smID ] ]);
        $result   = $mpms->getUniqueKeyGroup(PropertySchematic::init()->load([ 'smID' => $smID ]));
        $result_2 = $mpms->getUniqueKeyGroup(PropertySchematic::init()->load([ 'smID' => 'random' ]));
#        $this->assertTrue($result);
        #       $this->assertFalse($result_2);
    }
    
}
