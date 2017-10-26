<?php


namespace Sm\Core\Internal\Monitor;


/**
 * Class ShortTermMonitor
 *
 * Keeps track of history but forgets on changed task (name of whatever we're doing)
 */
class ShortTermMonitor extends Monitor {
    protected $taskID;
    
    /**
     * Set the Task that we are going to be working on
     *
     * @param $taskID
     *
     * @return $this
     */
    public function onTask($taskID) {
        if ($taskID !== $this->taskID) {
            $this->clear();
        }
        $this->taskID = $taskID;
        return $this;
    }
}