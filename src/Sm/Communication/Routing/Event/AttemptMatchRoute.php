<?php


namespace Sm\Communication\Routing\Event;


use Sm\Core\Event\Event;

class AttemptMatchRoute extends Event {
    private $request;
    private $route;
    /** @var bool */
    private $success;
    
    
    public function __construct($request, $route, $success = false) {
        parent::__construct();
        $this->request = $request;
        $this->route   = $route;
        $this->success = $success;
    }
    
    public static function init($request, $route, $success = false): AttemptMatchRoute {
        return new static($request, $route, $success);
    }
    
    function jsonSerialize() {
        return array_merge(parent::jsonSerialize(), [
            'request' => $this->request,
            'route'   => $this->route,
            'success' => $this->success,
        ]);
    }
    public function setSuccess(bool $success): AttemptMatchRoute {
        $this->success = $success;
        return $this;
    }
}