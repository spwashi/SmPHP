<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 12:46 AM
 */

//<editor-fold desc="TESTING PURPOSES ONLY">
use Sm\Communication\Network\Http\Request\HttpRequestFromEnvironment;
use Sm\Core\Context\Layer\StandardLayer;
use Sm\Core\System_\Sm;
use Sm\Http\Http;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);
error_reporting(-1);
//*/
//</editor-fold>

ob_start();
require_once __DIR__ . '/src/SmPHP/Sm.php';


/** @var \Sm\Communication\CommunicationLayer $CommunicationLayer */
$CommunicationLayer = Sm::$instance->getLayers()->resolve(StandardLayer::COMMUNICATION);
$Request            = $CommunicationLayer->resolveRequest();
$raw_response       = $CommunicationLayer->route($Request);
$CommunicationLayer->dispatch(Http::class, $raw_response);