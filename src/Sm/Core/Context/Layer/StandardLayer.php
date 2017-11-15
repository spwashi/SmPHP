<?php
/**
 * User: Sam Washington
 * Date: 6/23/17
 * Time: 2:25 PM
 */

namespace Sm\Core\Context\Layer;


use Sm\Core\Context\StandardContext;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Monitor\HasMonitorTrait;
use Sm\Core\Module\Module;
use Sm\Core\Module\ModuleContainer;

/**
 * Class StandardLayer
 *
 * @inheritdoc
 *
 * Represents standard functionality of a Layer
 *
 * @property \Sm\Core\Internal\Monitor\MonitorContainer $monitors
 *
 * @package Sm\Core\Context\Layer
 */
abstract class StandardLayer extends StandardContext implements Layer, \JsonSerializable {
    /** @var ModuleContainer $moduleContainer */
    protected $moduleContainer;
    /** @var array An array of the Layer Roots we checked applicability for */
    protected $checked_layer_root_ids = [];
    /** @var  LayerRoot $layerRoot */
    protected $layerRoot;
    
    use HasMonitorTrait;
    use HasObjectIdentityTrait;
    
    public function __construct() {
        parent::__construct();
    }
    public static function init() {
        return new static;
    }
    public function __get($name) {
        switch ($name) {
            case 'monitors':
                return $this->getMonitorContainer();
        }
        return null;
    }
    /**
     * Initialize the Layer on the LayerRoot provided
     *
     * @param LayerRoot $layerRoot
     *
     * @return $this
     */
    public function setRoot(LayerRoot $layerRoot) {
        $this->layerRoot = $layerRoot;
        return $this;
    }
    /**
     * Throw an error if the Module isn't one that we expect
     *
     * @param \Sm\Core\Module\Module $module
     *
     * @param                        $name
     *
     * @throws \Sm\Core\Module\Error\InvalidModuleException
     */
    public function checkCanRegisterModule(Module $module, $name): void { }
    public function getModuleContainer(): ModuleContainer {
        return $this->moduleContainer = $this->moduleContainer ?? new ModuleContainer;
    }
    /**
     * Register a Module under this Layer
     *
     * @param \Sm\Core\Module\ModuleProxy|\Sm\Core\Module\Module $module The Module that we are registering under this Layer
     * @param string                                             $name   The name that this Module will take under this Layer
     *
     * @return static
     */
    public function registerModule(Module $module, string $name) {
        $this->checkCanRegisterModule($module, $name);
        $proxy = $module->initialize($this);
        $this->getModuleContainer()->register($name, $proxy);
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
        return $this->getModuleContainer()->resolve($name);
    }
    /**
     * @return LayerRoot
     */
    public function getLayerRoot(): ?LayerRoot {
        return $this->layerRoot;
    }
    public function setModuleContainer(ModuleContainer $moduleContainer): StandardLayer {
        $this->moduleContainer = $moduleContainer;
        return $this;
    }
    public function __debugInfo() {
        return [
            'monitors' => $this->getMonitorContainer()->getAll(),
        ];
    }
    public function jsonSerialize() {
        return $this->__debugInfo();
    }
}