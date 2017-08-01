<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 12:47 AM
 */

namespace Sm\Core\Context\Layer;


use Sm\Core\Context\Proxy\StandardContextualizedProxy;

/**
 * Class LayerProxy
 *
 * Proxy for Layer Classes
 *
 * @package Sm\Core\Context\Layer
 */
class LayerProxy extends StandardContextualizedProxy implements Layer {
    /** @var Layer $subject The layer being proxied */
    protected $subject;
    /** @var \Sm\Core\Context\Layer\LayerRoot The LayerRoot that the proxy will do all actions relative to */
    protected $layerRoot;
    /**
     * LayerProxy constructor.
     *
     * @param Layer                            $layer     The Layer being proxied
     * @param \Sm\Core\Context\Layer\LayerRoot $layerRoot The LayerRoot using this Proxy
     */
    public function __construct(Layer $layer, LayerRoot $layerRoot) {
        parent::__construct($layer, $layerRoot);
        $this->setLayerRoot($layerRoot);
    }
    public function setLayerRoot(LayerRoot $layerRoot) {
        $this->layerRoot = $layerRoot;
        return $this;
    }
}