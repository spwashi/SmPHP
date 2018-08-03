<?php


namespace Sm\Data\Entity\Context;
use Sm\Core\Context\Context;
use Sm\Data\Entity\Entity;

/**
 * Entities created in the context of another
 */
class NestedEntityCreationContext extends EntityCreationContext {
    protected $parentContext;
    /** @var Entity */
    protected $parentEntity;
    public function setParentContext(Context $parentContext) {
        $this->parentContext = $parentContext;
        return $this;
    }
    public function setParentEntity(Entity $parentEntity) {
        $this->parentEntity = $parentEntity;
        return $this;
    }
}