<?php


namespace Sm\Modules\View\Twig;

use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Util;
use Sm\Modules\View\Twig\Exception\MissingEnvironmentException;
use Sm\Representation\Module\RepresentationModule;
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
    protected function twigToViewProxy($string, $vars = []): ViewProxy {
        $twigEnvironment = $this->getTwigEnvironment();
        $view            = TwigView::init()
                                   ->setTwigTemplate($string)
                                   ->setItem($vars)
                                   ->setTwigEnvironment($twigEnvironment);
        return ViewProxy::init($view);
    }
    /**
     * The
     *
     * @return \Sm\Modules\View\Twig\TwigViewModule
     */
    protected function registerDefaultRepresentationResolvers(): TwigViewModule {
        $twigViewModule = $this;
        $this->registerRepresentationResolvers(
            [
                /**
                 * @param string $string The name of the template. Just to get very basic templating going
                 * @param array  $vars   An object/array of variables that are going to go into the View as variables
                 */
                function ($template_name = null, $vars = []) {
                    # Only for function calls that are like 'twig_template_name.twig', $vars
                    if (!(is_string($template_name) && Util::endsWith($template_name, '.twig'))) return null;
                    return $this->twigToViewProxy($template_name, $vars);
                },
                /**
                 * @param string $string The name of the template. Just to get very basic templating going
                 * @param array  $vars   An object/array of variables that are going to go into the View as variables
                 */
                function ($use_twig_identifier = null, $template_name = null, $vars = []) {
                    # Only for function calls that are like 'twig_template_name.twig', $vars
                    if (!($use_twig_identifier === TwigView::class && is_string($template_name))) return null;
                    return $this->twigToViewProxy($template_name, $vars);
                },
            ]);
        
        return $this;
    }
    /**
     * Get the TwigEnvironment that we are going to use within some Context
     *
     * @return \Twig_Environment
     * @throws \Sm\Core\Exception\UnimplementedError
     * @throws \Sm\Modules\View\Twig\Exception\MissingEnvironmentException
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