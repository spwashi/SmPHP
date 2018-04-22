<?php


namespace Sm\Dummy\Module;


use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Context\Layer\Module\LayerModule;
use Sm\Dummy\DummyLayer;

abstract class DummyModule extends LayerModule {
    /**
     * @param null|\Sm\Dummy\DummyLayer|\Sm\Core\Context\Layer\Layer $context
     *
     * @throws \Sm\Core\Context\Exception\InvalidContextException
     */
    protected function establishContext(Layer $context = null) {
        if (!($context instanceof DummyLayer)) throw new InvalidContextException("Cannot register anything but a DummyLayer!");
        /** @var DummyLayer $context */
        parent::establishContext($context);
    }
}