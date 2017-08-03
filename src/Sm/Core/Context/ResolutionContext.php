<?php
/**
 * User: Sam Washington
 * Date: 6/19/17
 * Time: 8:41 PM
 */

namespace Sm\Core\Context;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Paths\PathContainer;

/**
 * Class ResolutionContext
 *
 * A Context that tells us about what we, in the development enviroment, have access to resolve.
 * Primarily Core things like PathContainers, etc
 *
 * @property-read PathContainer $paths
 */
class ResolutionContext extends StandardContext {
    protected $pathContainer;
    use HasObjectIdentityTrait;
    
    
    /**
     * ResolutionContext constructor.
     *
     * @see \Sm\Core\Context\ResolutionContext::$pathContainer
     * @see \Sm\Core\Context\ResolutionContext::$factoryContainer
     *
     * @param \Sm\Core\Paths\PathContainer $pathContainer
     */
    public function __construct(PathContainer $pathContainer) {
        parent::__construct();
        $this->setPathContainer($pathContainer);
    }
    /**
     * Set the PathContainer of this class for Path resolution
     *
     * @param \Sm\Core\Paths\PathContainer $pathContainer
     *
     * @return $this
     */
    public function setPathContainer(PathContainer $pathContainer) {
        $this->pathContainer = $pathContainer;
        return $this;
    }
}