<?php


namespace Sm\Representation\Module;


use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Context\Layer\Module\LayerModule;
use Sm\Core\Factory\Exception\FactoryCannotBuildException;
use Sm\Core\Util;
use Sm\Representation\Context\RepresentationContext;
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
    protected function _initialize(Layer $context = null) {
        if (!($context instanceof RepresentationLayer)) throw new InvalidContextException("Cannot register anything but a RepresentationLayer!");
        parent::_initialize($context);
    }
    
    /**
     * Create a representation of this item based on some sort of representation context
     *
     * @param                                                  $item
     * @param \Sm\Representation\Context\RepresentationContext $representationContext The context in which we are representing this item. #todo is there only one?
     *
     * @return mixed|null|\Sm\Representation\Representation
     * @throws \Sm\Representation\Exception\CannotRepresentException
     */
    public function represent($item, RepresentationContext $representationContext = null): Representation {
        try {
            return $this->representationFactory->resolve($item, $representationContext);
        } catch (FactoryCannotBuildException $exception) {
            throw new CannotRepresentException("There is no way to represent this item -- " . Util::getShape($item));
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