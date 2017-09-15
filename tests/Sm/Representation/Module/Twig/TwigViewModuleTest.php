<?php

namespace Sm\Representation\Module\Twig;


use Sm\Representation\RepresentationLayer;

const PATH_TO_TEMPLATES = '/var/www/SmPHP/tests/tmp/';


class TwigViewModuleTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolveView() {
        $loader              = new \Twig_Loader_Filesystem(PATH_TO_TEMPLATES);
        $twig_view_module    = new TwigViewModule(new \Twig_Environment($loader));
        $representationLayer = new RepresentationLayer;
        $representationLayer->registerModule($twig_view_module);
        $test = $representationLayer->render('hello');
        $this->assertEquals('THIS SHOULD SAY: hello', $test);
    }
}
