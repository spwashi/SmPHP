<?php


namespace Sm\Data\Entity;


use Sm\Core\Context\Context;
use Sm\Data\Entity\Context\ContextualizedEntityProxy;

trait EntityTrait {
    /**
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return \Sm\Data\Entity\Context\ContextualizedEntityProxy
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function proxyInContext(Context $context): ?EntitySchema {
        $contextualizedEntityProxy = new ContextualizedEntityProxy($this, $context);
        return $contextualizedEntityProxy;
    }
}