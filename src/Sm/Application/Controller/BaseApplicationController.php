<?php


namespace Sm\Application\Controller;


use Sm\Application\Application;
use Sm\Controller\BaseController;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Exception\InvalidArgumentException;

/**
 * Class BaseApplicationController
 *
 * @property Application app
 */
class BaseApplicationController extends BaseController implements ApplicationController {
    public function __get($name) {
        switch ($name) {
            case  'app':
                return $this->getApplication();
            
        }
        return null;
    }
    
    final public function setLayerRoot(LayerRoot $layerRoot) {
        if (!($layerRoot instanceof Application)) {
            throw new InvalidArgumentException("Trying to initialize an application controller in a non-application context");
        }
        return parent::setLayerRoot($layerRoot);
    }
    public function getApplication(): ?Application {
        return $this->layerRoot;
    }
}