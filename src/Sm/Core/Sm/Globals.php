<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 7/2/18
 * Time: 10:02 AM
 */

namespace Sm\Core\Sm;

use Sm\Core\Exception\InvalidArgumentException;


/**
 * Class Globals
 * @package Sm\Core\Sm
 *
 *
 * @property  $get
 * @property  $post
 * @property  $files
 * @property  $cookie
 * @property  $session
 * @property  $server
 * @property  $request
 * @property  $env
 */
class Globals {
	protected $get;
	protected $post;
	protected $files;
	protected $cookie;
	protected $session;
	protected $server;
	protected $request;
	protected $env;
	private   $initializing;
	public function enterInitMode() {
		$this->initializing = true;
		return $this;
	}
	public function exitInitMode() {
		$this->initializing = false;
		return $this;
	}

	public function __get($name) { return $this->$name = $this->$name ?? []; }
	public function __set($name, $value) {
		if (!property_exists($this, $name)) throw new InvalidArgumentException("Cannot interact with global var");
		if (!$this->initializing) throw new InvalidArgumentException("Cannot interact with global var");
		return $this->$name = $value;
	}
}