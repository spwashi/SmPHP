<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/25/18
 * Time: 9:58 AM
 */

namespace Sm\Logging;


interface Logger {
	public function log($item, string $name);
}