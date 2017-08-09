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
use Sm\Core\Context\StandardContext;
use Sm\Core\Exception\InvalidArgumentException;
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
    /** @var  LayerContainer */
    protected $layers;
    /**
     * Sm constructor.
     *
     * @param                                       $resolutionContext
     * @param LayerContainer                        $layers
     */
    public function __construct(LayerContainer $layers) {
        parent::__construct();
        $this->layers = $layers;
    }
    
    /**
     * Get all of the child Layers under this Layer Root.
     *
     * @return LayerContainer
     */
    public function getLayers(): LayerContainer {
        return $this->layers;
    }
    public function __get($name) {
        $layer = $this->getLayers()->resolve($name);
        if (!isset($layer)) throw new InvalidArgumentException("No Layer registered as {$name}");
        return $layer;
    }
}

Sm::$instance = new Sm(LayerContainer::init());

$routingModule      = new StandardRoutingModule;
$communicationLayer = new CommunicationLayer;
$communicationLayer->registerRoutingModule($routingModule)
                   ->registerModule(CommunicationLayer::HTTP_MODULE, new HttpCommunicationModule)
                   ->registerRoutes([ 'Sm' => StringResolvable::init('sam') ]);

Sm::$instance->getLayers()->register('communication', $communicationLayer);