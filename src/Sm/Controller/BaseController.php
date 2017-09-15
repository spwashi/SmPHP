<?php


namespace Sm\Controller;


use Sm\Core\Context\Layer\LayerRoot;

class BaseController implements Controller {
    protected $layerRoot;
    public function setLayerRoot(LayerRoot $layerRoot) {
        $this->layerRoot = $layerRoot;
        return $this;
    }
    public function getLayerRoot(): LayerRoot {
        return $this->layerRoot;
    }
}