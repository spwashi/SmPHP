<?php
/**
 * User: Sam Washington
 * Date: 6/26/17
 * Time: 9:40 PM
 */

namespace Sm\Core\Context\Layer;

use Sm\Core\Context\Context;
use Sm\Core\Context\ResolutionContext;

/**
 * Interface LayerRoot
 *
 * An interface that identifies this object as being the root of layers.
 * When layers communicate with each other, usually it will be throug this object.
 *
 * @package Sm\Core\Context\Layer
 */
interface LayerRoot extends Context {
    /**
     * Get all of the child Layers under this Layer Root.
     *
     * @return \Sm\Core\Context\Layer\LayerContainer
     */
    public function getLayers(): LayerContainer;
    public function getResolutionContext(): ResolutionContext;
}