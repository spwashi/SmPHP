<?php
/**
 * User: Sam Washington
 * Date: 3/1/17
 * Time: 8:23 PM
 */

namespace Sm\Data\Type\Variable_;

use Sm\Core\Resolvable\AbstractResolvable;
use Sm\Core\Resolvable\NativeResolvable;
use Sm\Data\Type\Variable_\Exception\InvalidVariableTypeError;

class Example_1 extends AbstractResolvable {
    public function resolve($_ = null) { return 1; }
}

class Example_2 extends AbstractResolvable {
    public function resolve($_ = null) { return 2; }
}

class Example_3 extends AbstractResolvable {
    public function resolve($_ = null) { return 3; }
}

class Variable_Test extends \PHPUnit_Framework_TestCase {
    /** @var  Variable_ $Variable_ */
    protected $Variable_;
    public function setUp() {
        $this->Variable_ = new Variable_();
    }
    public function testCanResolve() {
        $this->assertNull($this->Variable_->resolve());
        $this->Variable_->setValue(NativeResolvable::init(1));
        $this->assertEquals(1, $this->Variable_->resolve());
    }
    
    /**
     * Can we restrict this object from accepting values of improper type?
     *
     * @todo consider this error procedure
     */
    public function testPotentialTypes() {
        $this->Variable_->setPotentialTypes(Example_1::class, Example_2::class);
        $this->Variable_->setValue(new Example_1);
        $this->assertEquals(1, $this->Variable_->resolve());
        $this->Variable_->setValue(new Example_2);
        $this->assertEquals(2, $this->Variable_->resolve());
    
        $this->expectException(InvalidVariableTypeError::class);
        $this->Variable_->setValue(new Example_3);
    }
}
