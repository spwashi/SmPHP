<?php


namespace Sm\Controller;


use Sm\Core\Context\Layer\LayerRoot;

interface Controller {
	public function setLayerRoot(LayerRoot $layerRoot);
	public function getLayerRoot(): LayerRoot;
	public function proxy(): Controller;
}