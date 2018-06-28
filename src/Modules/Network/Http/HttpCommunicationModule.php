<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 5:14 PM
 */

namespace Sm\Modules\Network\Http;


use Sm\Communication\Module\TwoWayCommunicationModule;
use Sm\Communication\Request\Request;
use Sm\Communication\Routing\Route;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Modules\Network\Http\Request\HttpRequest;
use Sm\Modules\Network\Http\Request\HttpRequestDescriptor;
use Sm\Modules\Network\Http\Request\HttpRequestFromEnvironment;
use Sm\Modules\Network\Http\Response\HttpResponse;

/**
 * Class HttpCommunicationModule
 *
 * Meant to add the functionality of HTTP requests to the site
 *
 * @package Sm\Communication\Module
 */
class HttpCommunicationModule extends TwoWayCommunicationModule {
	/**
	 * @return array
	 */
	protected function getRequestResolutionMethods() {
		return [
			HttpRequestFromEnvironment::class => function () { return HttpRequestFromEnvironment::getRequestFromEnvironment(); },
			Http::class                       => function () { return HttpRequest::init(); },
		];
	}
	protected function getResponseResolutionMethods() {
		return [
			Http::class => function () { return new HttpResponse(); },
		];
	}
	protected function getResponseDispatchMethods() {
		return [
			Http::class =>
				function ($result) {
					if ($result instanceof Request) {
						throw new UnimplementedError("Can't do this kind of request yet!");
					}

					if ($result instanceof Route) {
						throw new UnimplementedError("Not sure how to handle this use case");
					}

					if (!($result instanceof HttpResponse)) {
						if (is_array($result) || $result instanceof \JsonSerializable) {
							if (!(defined('NO_JSON') && NO_JSON)) {
								$result = HttpResponse::init()->setBody(json_encode($result))->setContentType('application/json');
							} else {
								ob_start();
								var_dump($result);
								$body   = ob_get_clean();
								$result = HttpResponse::init()->setBody($body);
							}
						} else {
							$body   = StringResolvable::init($result);
							$result = HttpResponse::init()->setBody($body);
						}
					}

					$result->makeHeaders();

					echo $result->getBody();
				},

			Http::REDIRECT =>
				function ($route_or_request) {
					if (!($route_or_request instanceof Route)) {
						throw new UnimplementedError("Can only dispatch routes");
					}

					$route = $route_or_request;

					unset($route_or_request);

					$description = $route->getRequestDescriptor();

					if (!($description instanceof HttpRequestDescriptor)) {
						throw new UnimplementedError("HTTP Module can only dispatch HTTP routes");
					}

					$arguments = $route->getPrimedArguments();
					$url       = $description->asUrlPath($arguments);
					header("Location: {$url}");
				},
		];
	}

}