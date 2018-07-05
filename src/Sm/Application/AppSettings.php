<?php


namespace Sm\Application;


use Sm\Core\Container\Mini\MiniContainer;
use Sm\Core\Resolvable\Exception\UnresolvableException;

/**
 * Class AppSettings
 *
 * @property string path
 * @property string name
 *
 * @property string base_url
 */
class AppSettings extends MiniContainer {
	private $base_configuration;

	public function __get($name) {
		switch ($name) {
			case 'config':
				return $this->base_configuration;
		}
		throw new UnresolvableException("Cannot get {$name} from App Settings");
	}
	public function setBaseConfiguration(array $base_config) {
		$this->base_configuration = $base_config;
		return $this;
	}
}