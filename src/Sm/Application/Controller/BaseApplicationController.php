<?php


namespace Sm\Application\Controller;


use Sm\Application\Application;
use Sm\Controller\BaseController;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Exception\InvalidArgumentException;

class BaseApplicationController extends BaseController implements ApplicationController {
    public function setLayerRoot(LayerRoot $layerRoot) {
        if (!($layerRoot instanceof Application)) {
            throw new InvalidArgumentException("Trying to initialize an application controller in a non-application context");
        }
        return parent::setLayerRoot($layerRoot);
    }
    
    public function getApplication(): ?Application {
        return $this->layerRoot;
    }
}