<?php


namespace Sm\Controller;


use Sm\Core\Resolvable\AbstractResolvable;

class ControllerResolvable extends AbstractResolvable {
    /** @var  ControllerLayer $controllerLayer */
    protected $controllerLayer;
    public function resolveController_default() {
        var_dump($this->subject);
        var_dump(...func_get_args());
        return 'hello';
    }
    public function getControllerLayer():?ControllerLayer {
        return $this->controllerLayer;
    }
    public function setControllerLayer(ControllerLayer $controllerLayer) {
        $this->controllerLayer = $controllerLayer;
        return $this;
    }
    public function resolve() {
        $controllerLayer = $this->getControllerLayer();
        if (isset($controllerLayer)) {
            return $controllerLayer->getController($this->subject)->resolve(...func_get_args());
        } else {
            return $this->resolveController_default(...func_get_args());
        }
    }
}