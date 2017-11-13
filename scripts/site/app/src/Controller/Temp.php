<?php


namespace EXAMPLE_APP_NAMESPACE\Controller;


use Sm\Application\Controller\BaseApplicationController;

class Temp extends BaseApplicationController {
    public function react_1() {
        echo file_get_contents(EXAMPLE_APP__PATH . 'public/js/index.html');
    }
}