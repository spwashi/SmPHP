<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 5:11 PM
 */

namespace Sm\Communication\Module;


use Sm\Communication\CommunicationLayer;
use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Context\Layer\Module\LayerModule;

/**
 * Class CommunicationLayerModule
 *
 * Class meant to register items in the Request or Response Factories
 *
 * @package Sm\Communication\Module
 */
abstract class CommunicationModule extends LayerModule {
    /**
     * @param null|\Sm\Communication\CommunicationLayer|\Sm\Core\Context\Layer\Layer $context
     *
     * @throws \Sm\Core\Context\Exception\InvalidContextException
     */
    protected function _initialize(Layer $context = null) {
        if (!($context instanceof CommunicationLayer)) throw new InvalidContextException("Cannot register anything but a CommunicationLayer!");
        parent::_initialize($context);
        /** @var CommunicationLayer $context */
        $context->registerRequestResolvers($this->getRequestResolutionMethods());
        $context->registerRequestResolvers($this->getResponseResolutionMethods());
        $context->registerResponseDispatchers($this->getResponseDispatchMethods());
    }
    
    protected function getRequestResolutionMethods() { return []; }
    protected function getResponseResolutionMethods() { return []; }
    protected function getResponseDispatchMethods() { return []; }
}