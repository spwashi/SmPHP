<?php

use Sm\Representation\Module\Twig\TwigViewModule;

require_once EXAMPLE_APP__CONFIG_PATH . 'autoload/autoload.php';


####################################################################################
#####              This is what actually configures the app.                 #######
####################################################################################
const EXAMPLE_APP__URL            = 'http://localhost/wanghorn';
const EXAMPLE_APP__URL_PUBLIC     = EXAMPLE_APP__URL . '/public';
const EXAMPLE_APP__VIEW_TWIG_PATH = EXAMPLE_APP__PATH . 'app/view/twig/';


/** @var \Sm\Application\Application $app */
if (!isset($app)) {
    die("Cannot configure without an app");
}

$config_json = EXAMPLE_APP__CONFIG_PATH . '_generated/_config.json';

if (file_exists($config_json)) {
    $has_been_configured = $config_json;
}


#-----------------------------------------------------------------------------------
#   Controller Layer
#-----------------------------------------------------------------------------------

$app->controller->addControllerNamespace('\\EXAMPLE_APP_NAMESPACE\\Controller\\');

#-----------------------------------------------------------------------------------
#   Data Layer
#-----------------------------------------------------------------------------------

$data_json_path = EXAMPLE_APP__CONFIG_PATH . '_generated/data.json';

if (file_exists($config_json)) {
    $dataJson    = file_get_contents($data_json_path);
    $data_config = json_decode($dataJson, 1);
    $app->data->configure($data_config);
}


#-----------------------------------------------------------------------------------
#   Representation Layer
#-----------------------------------------------------------------------------------

$twig__Loader_Filesystem = new Twig_Loader_Filesystem([ EXAMPLE_APP__VIEW_TWIG_PATH, ]);
$twig__Environment       = new Twig_Environment($twig__Loader_Filesystem);

$twig__Environment->addGlobal('app_path__public', EXAMPLE_APP__URL_PUBLIC);

$twigViewModule = new TwigViewModule($twig__Environment);
$app->representation->registerModule($twigViewModule);