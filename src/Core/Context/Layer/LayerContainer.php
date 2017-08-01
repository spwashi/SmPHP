<?php
/**
 * User: Sam Washington
 * Date: 6/26/17
 * Time: 9:42 PM
 */

namespace Sm\Core\Context\Layer;


use Sm\Core\Container\Container;
use Sm\Core\Context\Layer\Exception\InaccessibleLayerException;
use Sm\Core\Exception\InvalidArgumentException;


/**
 * Class LayerContainer
 *
 * Simple Container for holding Layers
 *
 * @package Sm\Core\Context\Layer
 */
class LayerContainer extends Container {
    /**
     * @param mixed|null|string $name
     *
     * @inheritdoc
     * @return null|Layer
     */
    public function resolve($name = null) {
        $layer = parent::resolve($name);
        if (!isset($layer)) throw new InaccessibleLayerException("Cannot resolve {$name} layer.");
        return $layer;
    }
    /**
     * @param array|null|string                                  $name
     * @param callable|mixed|null|\Sm\Core\Resolvable\Resolvable $registrand
     *
     * @return $this|static
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function register($name = null, $registrand = null) {
        if (!(is_string($name))) throw new InvalidArgumentException("Invalid name");
        if (!($registrand instanceof StandardLayer)) throw new InvalidArgumentException("Cannot register non-Layer {$name} here");
        parent::register($name, $registrand);
        return $this;
    }
    
}