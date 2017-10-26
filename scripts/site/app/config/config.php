<?php

use Sm\Representation\Module\Twig\TwigViewModule;

require_once EXAMPLE_APP__CONFIG_PATH . 'autoload/autoload.php';


####################################################################################
#####              This is what actually configures the app.                 #######
####################################################################################
const EXAMPLE_APP__URL            = 'http://localhost/wanghorn';
const EXAMPLE_APP__URL_PUBLIC     = EXAMPLE_APP__URL . '/public';
const EXAMPLE_APP__VIEW_TWIG_PATH = EXAMPLE_APP__PATH . 'view/twig/';


/** @var \Sm\Application\Application $app */
if (!isset($app)) {
    die("Cannot configure without an app");
}


#-----------------------------------------------------------------------------------
#   Controller Layer
#-----------------------------------------------------------------------------------

$app->controller->addControllerNamespace('\\EXAMPLE_APP_NAMESPACE\\Controller\\');


#-----------------------------------------------------------------------------------
#   Representation Layer
#-----------------------------------------------------------------------------------

$twig__Loader_Filesystem = new Twig_Loader_Filesystem([ EXAMPLE_APP__VIEW_TWIG_PATH, ]);
$twig__Environment       = new Twig_Environment($twig__Loader_Filesystem);

$twig__Environment->addGlobal('app_path__public', EXAMPLE_APP__URL_PUBLIC);

$twigViewModule = new TwigViewModule($twig__Environment);
$app->representation->registerModule($twigViewModule);