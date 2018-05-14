<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 3:26 PM
 */

namespace Sm\Communication\Routing\Exception;


use Sm\Core\Internal\Monitor\Monitor;
use Sm\Core\Resolvable\Exception\UnresolvableException;

class RouteNotFoundException extends UnresolvableException {
    public function addAttemptedRouteMonitor(Monitor $monitor) {
        return $this->addMonitor('attempted_routes', $monitor);
    }
}