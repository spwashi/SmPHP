<?php


namespace EXAMPLE_APP_NAMESPACE\Controller;


use Sm\Application\Controller\BaseApplicationController;

class ErrorController extends BaseApplicationController {
    public function rt_404() {
        return func_get_args();
    }
}