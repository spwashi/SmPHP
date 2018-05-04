<?php


namespace Sm\Communication\Routing\Event;


use Sm\Communication\Routing\Route;
use Sm\Core\Event\Event;

class AddRoute extends Event {
    private $config;
    private $route;
    /** @var bool */
    private $success;
    
    
    public function __construct($config, Route $route = null, $success = false) {
        parent::__construct();
        $this->config  = $config;
        $this->route   = $route;
        $this->success = $success;
    }
    
    public static function init($config, Route $route = null, $success = false) {
        return new static($config, $route, $success);
    }
    
    function jsonSerialize() {
        return array_merge(parent::jsonSerialize(), [
            'success' => $this->success,
            'config'  => $this->config,
            'route'   => $this->route,
        ]);
    }
    public function setSuccess(bool $success): AddRoute {
        $this->success = $success;
        return $this;
    }
    public function setRoute(Route $route) {
        $this->route = $route;
        return $this;
    }
}