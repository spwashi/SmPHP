<?php


namespace Sm\Controller;


use Sm\Core\Resolvable\AbstractResolvable;
use Sm\Core\Resolvable\Error\UnresolvableException;

class ControllerResolvable extends AbstractResolvable {
    /** @var  ControllerLayer $controllerLayer */
    protected $controllerLayer;
    public function resolveController_default() {
        throw new UnresolvableException("Can't get a controller without a controller layer!");
    }
    public function getControllerLayer(): ?ControllerLayer {
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
    public function jsonSerialize() {
        return $this->subject;
    }
}