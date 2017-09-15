<?php


namespace Sm\Application\Controller;


use Sm\Application\Application;
use Sm\Controller\Controller;

/**
 * Interface ApplicationController
 * Controllers that are registered specifically on an Application
 */
interface ApplicationController extends Controller {
    public function getApplication(): ?Application;
}