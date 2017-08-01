<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:04 PM
 */

namespace Sm\Core\System_;


use Sm\Communication\CommunicationLayer;
use Sm\Communication\Module\HttpCommunicationModule;
use Sm\Communication\Routing\Module\StandardRoutingModule;
use Sm\Core\Context\Layer\LayerContainer;
use Sm\Core\Context\Layer\LayerRoot;
use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\Context\ResolutionContext;
use Sm\Core\Context\StandardContext;
use Sm\Core\Paths\PathContainer;
use Sm\Core\Resolvable\StringResolvable;

/**
 * Class Sm
 *
 * @property CommunicationLayer communication
 *
 */
class Sm extends StandardContext implements LayerRoot {
    /** @var  Sm $instance */
    public static $instance;
    /** @var  $resolutionContext */
    protected $resolutionContext;
    /** @var  LayerContainer */
    protected $layers;
    /**
     * Sm constructor.
     *
     * @param                                       $resolutionContext
     * @param LayerContainer                        $layers
     */
    public function __construct($resolutionContext, LayerContainer $layers) {
        parent::__construct();
        $this->resolutionContext = $resolutionContext;
        $this->layers            = $layers;
    }
    
    /**
     * Get all of the child Layers under this Layer Root.
     *
     * @return LayerContainer
     */
    public function getLayers(): LayerContainer {
        return $this->layers;
    }
    public function getResolutionContext(): ResolutionContext {
        return $this->resolutionContext;
    }
    public function __get($name) {
        if ($name === 'communication') return $this->getLayers()->resolve(StandardLayer::COMMUNICATION);
    }
}

$pathContainer     = new PathContainer;
$resolutionContext = new ResolutionContext($pathContainer);
Sm::$instance      = new Sm($resolutionContext, new LayerContainer);

$routingModule      = new StandardRoutingModule;
$communicationLayer = new CommunicationLayer;
$communicationLayer->registerRoutingModule($routingModule)
                   ->registerModule(CommunicationLayer::HTTP_MODULE, new HttpCommunicationModule)
                   ->registerRoutes([ 'Sm' => StringResolvable::init('sam') ]);

Sm::$instance->getLayers()->register('Communication', $communicationLayer);