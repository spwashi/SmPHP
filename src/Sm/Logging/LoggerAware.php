<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/25/18
 * Time: 9:56 AM
 */

namespace Sm\Logging;


interface LoggerAware {
	public function setLogger(Logger $logger);
}