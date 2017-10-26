<?php


namespace Sm\Core\Internal\Monitor;


use Sm\Core\Container\Mini\MiniContainer;

/**
 * Class MonitorContainer
 *
 * @property \Sm\Core\Internal\Monitor\Monitor info
 */
class MonitorContainer extends MiniContainer {
    
    public function resolve($name = null): Monitor {
        $item = parent::resolve($name);
        if (!isset($item)) {
            $item = new Monitor;
            $this->register($name, $item);
        }
        
        return $item;
    }
    
    public function getContents($name) {
        $item = $this->resolve($name);
        return $item->dump();
    }
}