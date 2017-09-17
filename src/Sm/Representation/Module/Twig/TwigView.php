<?php


namespace Sm\Representation\Module\Twig;


use Sm\Core\Context\Context;
use Sm\Core\Exception\UnimplementedError;
use Sm\Representation\Module\Twig\Exception\MissingEnvironmentException;
use Sm\Representation\View\View;

/**
 * Class TwigView
 *
 *
 * Views that are used for the TWIG templating system
 *
 */
class TwigView extends View {
    /** @var  mixed $item The thing we're rendering */
    protected $item;
    protected $template;
    /** @var  \Twig_Environment $twigEnvironment */
    private $twigEnvironment;
    #
    ##  Constructors/Initialization
    public static function init() { return new static; }
    
    #
    ##  Twig Environment
    public function getTwigEnvironment(): \Twig_Environment {
        return $this->twigEnvironment;
    }
    public function setTwigEnvironment(\Twig_Environment $twigEnvironment): TwigView {
        $this->twigEnvironment = $twigEnvironment;
        return $this;
    }
    
    #
    ##  Getters and Setters
    public function setItem($item) {
        $this->item = $item;
        return $this;
    }
    
    #
    ##  Rendering
    /**
     * Represent whatever we are depicting as a string
     *
     * @param Context|null $context If we are doing something within a Context,
     *
     * @return string
     * @throws \Sm\Representation\Module\Twig\Exception\MissingEnvironmentException
     * @internal param mixed $item The thing that we are going to render
     *
     */
    public function render($context = null): string {
        if (!isset($this->twigEnvironment)) throw new MissingEnvironmentException("Cannot find template without an environment");
        $variables       = $this->getItemVars();
        $twigEnvironment = $this->twigEnvironment;
        $template        = $this->getTwigTemplate();
        return $twigEnvironment->render($template, $variables);
    }
    /**
     * Function to get the variables we're looking for from the item we're rendering
     *
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    protected function getItemVars(): array {
        $item = $this->item;
        
        if (!isset($item)) return [];
        if (is_string($item)) return static::getTemplateValueArrayFromString($item);
    
        return $item;
    }
    
    #
    ##  Templating
    public function setTwigTemplate(string $twig_template) {
        $this->template = $twig_template;
        return $this;
    }
    protected function getTwigTemplate(): string {
        if (isset($this->template)) return $this->template;
        throw new UnimplementedError("Can't find templates for this yet");
    }
    /**
     * Convert a string into an array of variables suitable for this template
     *
     * @param string $item
     *
     * @return array
     */
    protected static function getTemplateValueArrayFromString(string $item): array {
        return [ 'item' => $item ];
    }
}