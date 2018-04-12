<?php


namespace Sm\Representation;


use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\Event\GenericEvent;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Module\Error\InvalidModuleException;
use Sm\Core\Module\Module;
use Sm\Core\Util;
use Sm\Representation\Exception\CannotRepresentException;
use Sm\Representation\Factory\RepresentationFactory;
use Sm\Representation\Module\RepresentationModule;
use Sm\Representation\View\Proxy\ViewProxy;

class RepresentationLayer extends StandardLayer {
    const LAYER_NAME              = 'representation';
    const MONITOR__REPRESENTATION = 'monitor.representation';
    /** @var  \Sm\Representation\Factory\RepresentationFactory $representationFactory */
    protected $representationFactory;
    /** @var array $representation_module_names An array of the names of Modules that we've made */
    protected $representation_module_names = [];
    
    
    /**
     * RepresentationLayer constructor.
     *
     * @param \Sm\Representation\Factory\RepresentationFactory|null $representationFactory THe factory that will be useed to resolve ... ?
     */
    public function __construct(RepresentationFactory $representationFactory = null) {
        parent::__construct();
    }
    
    
    public function registerModule(Module $module, string $name = null) {
        $name = $name ?? get_class($module);
        
        parent::registerModule($module, $name);
        
        $this->representation_module_names[] = $name;
        return $this;
        
    }
    public function checkCanRegisterModule(Module $module, $name): void {
        if (!($module instanceof RepresentationModule)) {
            throw new InvalidModuleException("Can only register RepresentationModules on this class");
        }
        parent::checkCanRegisterModule($module, $name);
    }
    
    
    /**
     * Create a representation of whatever we pass in
     *
     * @return \Sm\Representation\Representation
     * @throws \Sm\Representation\Exception\CannotRepresentException
     */
    public function represent(): Representation {
        $modules_from_end = array_reverse($this->representation_module_names, true);
        $modules          = $this->getModuleContainer();
        
        foreach ($modules_from_end as $module_name) {
            /** @var \Sm\Representation\Module\RepresentationModule $representationModule */
            $representationModule = $modules->{$module_name};
            try {
                $result = $representationModule->represent(...func_get_args());
                $this->monitors->{RepresentationLayer::MONITOR__REPRESENTATION}->append(GenericEvent::init('use ' . $module_name));
                return $result;
            } catch (CannotRepresentException $exception) {
            }
        }
        throw new CannotRepresentException("Cannot find suitable Representation method in modules " . json_encode($modules_from_end));
    }
    
    /**
     * Represent something and Render it
     *
     * @param                                                       $item
     *
     * @return string
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function render($item): string {
        $representation = $this->represent(...func_get_args());
        if ($representation instanceof ViewProxy) {
            return $representation->render();
        } else if (Util::canBeString($representation)) {
            return "{$representation}";
        } else {
            throw new UnimplementedError("No stringifiable representation interface");
        }
    }
}