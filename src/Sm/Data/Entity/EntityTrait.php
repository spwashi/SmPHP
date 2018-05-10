<?php


namespace Sm\Data\Entity;


use Sm\Core\Context\Context;

trait EntityTrait {
    /**
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return \Sm\Data\Entity\ContextualizedEntityProxy
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function proxyInContext(Context $context): ?ContextualizedEntityProxy {
        $contextualizedEntityProxy = new ContextualizedEntityProxy($this, $context);
        return $contextualizedEntityProxy;
    }
}