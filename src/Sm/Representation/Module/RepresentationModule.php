<?php


namespace Sm\Representation\Module;


use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Context\Layer\Module\LayerModule;
use Sm\Core\Factory\Exception\FactoryCannotBuildException;
use Sm\Core\Util;
use Sm\Representation\Exception\CannotRepresentException;
use Sm\Representation\Factory\RepresentationFactory;
use Sm\Representation\Representation;
use Sm\Representation\RepresentationLayer;

/**
 * Class RepresentationModule
 *
 * Module that will help represent items
 */
abstract class RepresentationModule extends LayerModule {
    /** @var  \Sm\Representation\Factory\RepresentationFactory $representationFactory */
    protected $representationFactory;
    
    
    public function __construct() {
        parent::__construct();
        $this->representationFactory = $representationFactory ?? RepresentationFactory::init();
    }
    
    /**
     * @param null|\Sm\Representation\RepresentationLayer|\Sm\Core\Context\Layer\Layer $context
     *
     * @throws \Sm\Core\Context\Exception\InvalidContextException
     */
    protected function establishContext(Layer $context = null) {
        if (!($context instanceof RepresentationLayer)) throw new InvalidContextException("Cannot register anything but a RepresentationLayer!");
        parent::establishContext($context);
    }
    
    /**
     * Create a representation of this item based on some sort of representation context
     *
     * @return \Sm\Representation\Representation
     * @throws \Sm\Representation\Exception\CannotRepresentException
     * @internal param $item
     *
     */
    public function represent(): Representation {
        try {
            return $this->representationFactory->resolve(...func_get_args());
        } catch (FactoryCannotBuildException $exception) {
            throw new CannotRepresentException("There is no way to represent this item -- " . Util::getShape(...func_get_args()));
        }
    }
    
    
    /**
     * As you would with a factory, register functions that we might use to resolve Representations w/r to this Module
     *
     * @param array $resolvers An array, indexed by an optional identifier, containing the methods that we are going to use to resolve Representations
     *
     * @return $this
     */
    public function registerRepresentationResolvers(array $resolvers) {
        $this->representationFactory->register($resolvers);
        return $this;
    }
}