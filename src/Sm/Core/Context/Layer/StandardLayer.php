<?php
/**
 * User: Sam Washington
 * Date: 6/23/17
 * Time: 2:25 PM
 */

namespace Sm\Core\Context\Layer;


use Sm\Core\Context\StandardContext;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Module\Error\InvalidModuleException;
use Sm\Core\Module\Module;
use Sm\Core\Module\ModuleContainer;

/**
 * Class StandardLayer
 *
 * @inheritdoc
 *
 * Represents standard functionality of a Layer
 *
 * @package Sm\Core\Context\Layer
 */
abstract class StandardLayer extends StandardContext implements Layer {
    const COMMUNICATION = 'Communication';
    
    /** @var \Sm\Core\Module\ModuleContainer $ModuleContainer */
    protected $ModuleContainer;
    /** @var array An array of the Layer Roots we checked applicability for */
    protected $checked_layer_root_ids = [];
    /** @var  LayerRoot $layerRoot */
    protected $layerRoot;
    
    use HasObjectIdentityTrait;
    
    public function __construct(ModuleContainer $moduleContainer = null) {
        $this->ModuleContainer = $moduleContainer ?? new ModuleContainer;
        parent::__construct();
    }
    /**
     * Initialize the Layer on the LayerRoot provided
     *
     * @param LayerRoot $layerRoot
     *
     * @return mixed
     */
    public function setRoot(LayerRoot $layerRoot) {
        $this->layerRoot = $layerRoot;
        return $this;
    }
    /**
     * Throw an error if the Module isn't one that we expect
     *
     * @param                        $name
     * @param \Sm\Core\Module\Module $module
     *
     * @throws \Sm\Core\Module\Error\InvalidModuleException
     */
    public function checkCanRegisterModule($name, Module $module) {
        $expected_modules = $this->_listExpectedModules();
        if (!in_array($name, $expected_modules)) {
            $st_class = static::class;
            throw new InvalidModuleException("Cannot register module {$name} on layer {$st_class}");
        }
    }
    /**
     * Register a Module under this Layer
     *
     * @param string                                             $name   The name that this Module will take under this Layer
     * @param \Sm\Core\Module\ModuleProxy|\Sm\Core\Module\Module $module The Module that we are registering under this Layer
     *
     * @return static
     * @throws \Sm\Core\Module\Error\InvalidModuleException If we try to register a Module that we actually can't
     */
    public function registerModule(string $name, Module $module) {
        $this->checkCanRegisterModule($name, $module);
        $proxy = $module->initialize($this);
        $this->ModuleContainer->register($name, $proxy);
        return $this;
    }
    
    /**
     * Get a Module from the Layer.
     *
     * @param string $name
     *
     * @return null|\Sm\Core\Module\Module
     */
    protected function getModule(string $name) {
        return $this->ModuleContainer->resolve($name);
    }
    /**
     * @return array An array of strings corresponding to the names of the modules that this layer needs to have
     */
    protected function _listExpectedModules(): array {
        return [];
    }
    /**
     * @return LayerRoot
     */
    public function getLayerRoot(): ?LayerRoot {
        return $this->layerRoot;
    }
}