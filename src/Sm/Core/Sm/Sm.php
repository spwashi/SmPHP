<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 7/2/18
 * Time: 9:59 AM
 */

namespace Sm\Core\Sm;


class Sm {
	/** @var Globals */
	public static $globals;
	const MODE__HTTP    = 'HTTP';
	const MODE__CONSOLE = 'CONSOLE';

	public static function init() {
		# Initialize the globals
		Sm::setMode(php_sapi_name() === 'cli' ? Sm::MODE__CONSOLE : Sm::MODE__HTTP);
	}

	public static function setMode($mode = Sm::MODE__HTTP) {
		static::$globals = (new Globals)->enterInitMode();
		switch ($mode) {
			case Sm::MODE__HTTP:
				static::$globals->server  = $_SERVER;
				static::$globals->get     = $_GET;
				static::$globals->post    = $_POST;
				static::$globals->files   = $_FILES;
				static::$globals->cookie  = $_COOKIE;
				static::$globals->session = $_SESSION;
				static::$globals->env     = $_ENV;
				static::$globals->request = $_REQUEST;
				break;
			case Sm::MODE__CONSOLE:
				break;
		}

		static::$globals->exitInitMode();
	}
}
Sm::init();