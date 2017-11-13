<?php

define('SM_IS_CLI', php_sapi_name() === 'cli');
error_reporting(E_ALL);

const EXAMPLE_APP__PATH        = __DIR__ . '/../';
const EXAMPLE_APP_SRC_PATH     = __DIR__ . '/src/';
const EXAMPLE_APP__CONFIG_PATH = __DIR__ . '/config/';


use Sm\Application\Application;
use Sm\Communication\CommunicationLayer;
use Sm\Communication\Network\Http\Http;
use Sm\Communication\Request\NamedRequest;
use Sm\Communication\Routing\Exception\RouteNotFoundException;

require_once 'vendor/autoload.php';

$app = Application::init('spwashi', EXAMPLE_APP__PATH);

try {
#   - Create & Boot the application
    $app = $app->boot();

#   - Create and dispatch the response
    $response = $app->communication->route(CommunicationLayer::ROUTE_RESOLVE_REQUEST);
    $result   = $app->communication->dispatch(Http::class, $response) ?? null; # meaningless if dispatch only prints
} catch (RouteNotFoundException $exception) {
#   - Create the error response
    $parentRequest  = $app->communication->resolveRequest();
    $request        = NamedRequest::init('rt_404')
                                  ->setParentRequest($parentRequest);
    $error_response = $app->communication->route($request, [ 'error' => 403 ]);
    $result         = $app->communication->dispatch(Http::class, $error_response) ?? null; # meaningless if dispatch only prints
} catch (Exception $exception) {
    echo '<pre>';
    print_r($exception);
    echo '</pre>';
}