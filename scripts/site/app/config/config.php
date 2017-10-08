<?php

# This is what actually configures the app.

/** @var \Sm\Application\Application $app */

use Sm\Representation\Module\Twig\TwigViewModule;

require_once APP_CONFIG . 'autoload/autoload.php';

if (!isset($app)) die("Cannot configure without an app");
$app->controller->addControllerNamespace('\\Spwashi\\Controller\\');

$routes = require_once APP_CONFIG . 'routes/routes.php';
$app->communication->registerRoutes($routes);
$app->representation->registerModule(new TwigViewModule(new Twig_Environment(new Twig_Loader_Filesystem([
                                                                                                            APP_PATH . 'app/views/',
                                                                                                        ]))));