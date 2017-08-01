<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 4:03 PM
 */

namespace Sm\Core\Internal\Identification;

/**
 * Class HasObjectIdentityTraitTest
 *
 * @package Sm\Core\Internal\Identification
 * @covers  \Sm\Core\Internal\Identification\Identifier
 */
class HasObjectIdentityTraitTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreateSelfID() {
        $object_identity_haver = new ObjectIdentityHaverStub();
        $object_id             = $object_identity_haver->getObjectId();
        $this->assertInternalType('string', $object_id);
    }
    
    /**
     * @covers \Sm\Core\Internal\Identification\Identifier::combineObjectIds()
     */
    public function testCanCombineObjectIDs() {
        $object_identity_haver   = new ObjectIdentityHaverStub;
        $object_identity_haver_1 = new ObjectIdentityHaverStub;
        $object_identity_haver_2 = new ObjectIdentityHaverStub;
        $this->assertNotEquals($object_identity_haver->getObjectId(), $object_identity_haver_1->getObjectId());
        $this->assertNotEquals($object_identity_haver_2->getObjectId(), $object_identity_haver_1->getObjectId());
        $this->assertNotEquals($object_identity_haver_2->getObjectId(), $object_identity_haver->getObjectId());
        $object_ids = Identifier::combineObjectIds(
            $object_identity_haver->getObjectId(),
            $object_identity_haver_1->getObjectId(),
            $object_identity_haver_2->getObjectId());
        $this->assertInternalType('string',
                                  $object_ids);
    }
    
}


class ObjectIdentityHaverStub implements Identifiable {
    use HasObjectIdentityTrait;
    public function __construct() {
        $this->createSelfID();
    }
}