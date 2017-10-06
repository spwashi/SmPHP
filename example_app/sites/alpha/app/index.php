<?php

define('SM_IS_CLI', php_sapi_name() === 'cli');
error_reporting(E_ALL);

const APP_PATH   = __DIR__ . '/';
const APP_SRC    = __DIR__ . '/src/';
const APP_CONFIG = __DIR__ . '/config/';


use Sm\Application\Application;
use Sm\Communication\CommunicationLayer;
use Sm\Communication\Network\Http\Http;
use Sm\Communication\Routing\Exception\RouteNotFoundException;

require_once 'vendor/autoload.php';

$app = Application::init('spwashi', APP_PATH);

try {
#   - Create & Boot the application
    $app = $app->boot();

#   - Create and dispatch the response
    $response = $app->communication->route(CommunicationLayer::ROUTE_RESOLVE_REQUEST);
    $result   = $app->communication->dispatch(Http::class, $response) ?? null; # meaningless if dispatch only prints
} catch (RouteNotFoundException $exception) {
    echo 'here';
#   - Create the error response
    $error_response = $app->communication->route('rt_404');
    $result   = $app->communication->dispatch(Http::class, $error_response) ?? null; # meaningless if dispatch only prints
} catch (Exception $exception) {
    echo '<pre>';
    print_r($exception);
    echo '</pre>';
}