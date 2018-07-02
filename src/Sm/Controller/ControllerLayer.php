<?php


namespace Sm\Controller;


use Sm\Controller\Event\Attempt_ResolveControllerClass;
use Sm\Controller\Exception\MalformedControllerException;
use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Util;

class ControllerLayer extends StandardLayer {
	# -- class constants --
	const LAYER_NAME                = 'controller';
	const MONITOR__CLASS_RESOLUTION = 'class_resolution';

	# -- class properties
	protected $default_controller_name = 'Controller';
	protected $controller_namespaces   = [
		__NAMESPACE__,
	];


	/**
	 * Creates a Resolvable that will eventually resolve to a controller registered on/accessible to this Layer.
	 *
	 * @param string|mixed $identifier The identifier that will get normalized
	 *
	 * @return \Sm\Controller\ControllerResolvable
	 */
	public function createControllerResolvable($identifier): ControllerResolvable {
		$identifier = $this->normalizeIdentifier($identifier);
		return ControllerResolvable::init($identifier)->setControllerLayer($this);
	}
	/**
	 * @see ControllerLayer::createControllerResolvable()
	 * @param string $identifier
	 * @return ControllerResolvable
	 */
	public function get(string $identifier): ControllerResolvable {
		return $this->createControllerResolvable($identifier);
	}
	/**
	 * Create a function resolvable from a normalized controller identifier (Class::method)
	 *
	 *
	 * @param $controller_identifier
	 *
	 * @return \Sm\Core\Resolvable\Resolvable
	 * @throws \Sm\Controller\Exception\MalformedControllerException
	 * @throws \Sm\Core\Exception\InvalidArgumentException
	 */
	public function getController($controller_identifier): Resolvable {
		if (!is_string($controller_identifier)) {
			$type = Util::getShape($controller_identifier);
			$id   = json_encode($controller_identifier);
			throw new InvalidArgumentException("Cannot resolve Controller Identifier '{$type}' - {$id}");
		}

		if (strpos($controller_identifier, '::') === false) {
			throw new MalformedControllerException("{$controller_identifier} not formed like a Controller. - is this a class, method, or function?");
		}

		list($class_name, $method) = explode('::', $controller_identifier);

		$this->resolveControllerNamespace($class_name);

		if (!method_exists($class_name, $method)) {
			throw new MalformedControllerException("{$class_name}::{$method} not found");
		}

		$instance = $this->initController($class_name);


		return FunctionResolvable::init([$instance, $method]);
	}
	/**
	 * Make sure the method identifier that we use is normal
	 *
	 * @param string $identifier The @ symbol identifies that we are dealing with something suffixed with Controller and in one of the controller namespaces.
	 *                           Everything prefaced with a # will get the first matching controller namespace appended to it.
	 *
	 * @return mixed
	 * @throws \Sm\Core\Exception\UnimplementedError
	 */
	protected function normalizeIdentifier($identifier) {
		if (!is_string($identifier)) throw new UnimplementedError("Cannot create a controller from anything except for a string");


		# Replace pipes with backslashes (makes it visually easier, I think)
		$identifier = str_replace('|', '\\', $identifier);

		# Sometimes we might
		preg_match('~\[([a-zA-Z]+)\]~', $identifier, $matches);
		if ($matches[1] ?? false) {
			$identifier = str_replace($matches[0], $matches[1] . '\\' . $matches[1], $identifier);
		}


		# if a controller is prefaced by a # sign, we use the controller namespace to resolve it.

		#  '@' methods are all suffixed with the word "controller".
		$at_location = strpos($identifier, '@');

		# return the default method
		if ($at_location === false) return $identifier;

		if ($at_location === 0) {
			# if the @ is in the first position, assume we intend to use the default controller to handle this method
			$method_array = [$this->default_controller_name, $identifier];
		} else {
			$method_array    = explode('@', $identifier);
			$method_array[0] .= 'Controller';
		}

		return '#' . join('::', $method_array);
	}

	/**
	 * Get an array of the potential namespaces in which we might find this Controller
	 *
	 * @return array
	 */
	public function getControllerNamespaces(): array {
		return $this->controller_namespaces;
	}
	public function addControllerNamespace(...$controller_namespaces) {
		$this->controller_namespaces = array_merge($this->controller_namespaces, $controller_namespaces);
		return $this;
	}
	/**
	 * @param $class_name
	 */
	protected function resolveControllerNamespace(&$class_name): void {
		if (strpos($class_name, '#') === 0) {
			$class_name      = substr($class_name, 1);
			$class           = null;
			$namespace_array = array_reverse($this->controller_namespaces);

			/** @var \Sm\Core\Internal\Monitor\Monitor $class_resolution__Monitor */
			$class_resolution__Monitor = $this->monitors->{ControllerLayer::MONITOR__CLASS_RESOLUTION};

			foreach ($namespace_array as $namespace) {
				$new_class_name = '\\' . trim($namespace, '\\') . '\\' . $class_name;

				$attempt = Attempt_ResolveControllerClass::init($new_class_name);

				$class_resolution__Monitor->append($attempt);

				if (!class_exists($new_class_name)) {
					continue;
				}
				$class_name = $new_class_name;
				$attempt->declareSuccessful();
				break;
			}
		}
	}
	/**
	 * Initialize the Controller  with all of the things this framework decides is necessary for the controller to know about.
	 * Usually just the Layer Root, but who knows where that'll go
	 *
	 * @param $class_name
	 *
	 * @return \Sm\Controller\Controller|object
	 */
	protected function initController($class_name) {
		$instance = new $class_name;
		if (is_a($class_name, Controller::class, true)) {
			/** @var \Sm\Controller\Controller $instance */
			if (isset($this->layerRoot)) {
				$instance->setLayerRoot($this->layerRoot);
			}
			return $instance->proxy();
		}
		return $instance;
	}
}