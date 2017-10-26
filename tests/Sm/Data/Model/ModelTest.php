<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 10:22 AM
 */

namespace Sm\Data\Model;


use Sm\Data\Property\Property;
use Sm\Data\Property\PropertySchemaContainer;

/**
 * Interface t_PropertyContainer
 *
 * @property Property $title
 */
interface t_PropertyContainer {

}

/**
 * Interface t_Model
 *
 * @property t_PropertyContainer|\Sm\Data\Property\PropertyContainer $properties
 */
interface t_Model {

}

class ModelTest extends \PHPUnit_Framework_TestCase {
    public function testModelCanHaveProperties() {
        $model      = new Model;
        $properties = $model->getProperties();
        $this->assertInstanceOf(PropertySchemaContainer::class, $properties);
    }
    public function testModelCanSetProperties() {
        $model = new Model;
        $model->getProperties()
              ->register('title',
                         new Property('title'));
        
        $this->assertNull($model->getProperties()->id);
        $this->assertInstanceOf(Property::class, $model->getProperties()->resolve('title'));
        $this->assertInstanceOf(Property::class, $model->properties->title);
    }
    public function testModelCanSetPropertyValues() {
        /** @var t_Model|Model $model */
        $model = new Model;
        $model->properties->register('title', new Property);
        $model->properties->title = 'test';
        $this->assertEquals('test', $model->properties->title->value);
        $model->properties->title = 'yellow';
    
        echo json_encode($model->properties->title->valueHistory, JSON_PRETTY_PRINT);
    }
}
