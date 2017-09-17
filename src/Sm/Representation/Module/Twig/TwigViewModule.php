<?php


namespace Sm\Representation\Module\Twig;

use Sm\Core\Exception\UnimplementedError;
use Sm\Representation\Module\RepresentationModule;
use Sm\Representation\Module\Twig\Exception\MissingEnvironmentException;
use Sm\Representation\View\Proxy\ViewProxy;

/**
 * Class TwigRepresentationModule
 *
 * Creates
 *
 */
class TwigViewModule extends RepresentationModule {
    private $twigEnvironments;
    private $_defaultTwigEnvironment;
    
    /**
     * TwigViewModule constructor.
     *
     * @param \Twig_Environment|null $defaultEnvironment The Twig Environment that we are going to use as a default to this TwigView Module
     */
    public function __construct(\Twig_Environment $defaultEnvironment = null) {
        parent::__construct();
        if (isset($defaultEnvironment)) $this->setDefaultTwigEnvironment($defaultEnvironment);
        $this->registerDefaultRepresentationResolvers();
    }
    
    /**
     * The
     *
     * @return \Sm\Representation\Module\Twig\TwigViewModule
     */
    protected function registerDefaultRepresentationResolvers(): TwigViewModule {
        $twigViewModule = $this;
        $this->registerRepresentationResolvers(
            [
                /**
                 * Typical Twig View renderer.
                 * Doesn't have an index in this array because we want to run it as a (kind of) last resort as it is the most general
                 * #todo
                 *
                 */
                function ($item = null, $vars = []) {
                    $twigEnvironment = $this->getTwigEnvironment();
                    $view            = TwigView::init()
                                               ->setItem($item)
                                               ->setTwigEnvironment($twigEnvironment);
        
        
                    return ViewProxy::init($view, $this->representationContext);
                },
            ]);
        
        return $this;
    }
    /**
     * Get the TwigEnvironment that we are going to use within some Context
     *
     * @return \Twig_Environment
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Representation\Module\Twig\Exception\MissingEnvironmentException
     */
    protected function getTwigEnvironment(): \Twig_Environment {
        if (isset($this->twigEnvironments)) throw new UnimplementedError("Can't get twig environments yet");
        
        if (!isset($this->_defaultTwigEnvironment)) throw new MissingEnvironmentException("There is no available Twig Environment by default");
        
        return $this->_defaultTwigEnvironment;
    }
    /**
     * @param mixed $defaultTwigEnvironment
     *
     * @return TwigViewModule
     */
    public function setDefaultTwigEnvironment($defaultTwigEnvironment) {
        $this->_defaultTwigEnvironment = $defaultTwigEnvironment;
        return $this;
    }
}