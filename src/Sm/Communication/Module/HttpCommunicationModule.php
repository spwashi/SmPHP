<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 5:14 PM
 */

namespace Sm\Communication\Module;


use Sm\Communication\Network\Http\Http;
use Sm\Communication\Network\Http\Request\HttpRequest;
use Sm\Communication\Network\Http\Request\HttpRequestFromEnvironment;
use Sm\Communication\Network\Http\Response\HttpResponse;
use Sm\Communication\Request\Request;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\StringResolvable;

/**
 * Class HttpCommunicationModule
 *
 * Meant to add the functionality of HTTP requests to the site
 *
 * @package Sm\Communication\Module
 */
class HttpCommunicationModule extends CommunicationModule {
    /**
     * @return array
     */
    protected function getRequestResolutionMethods() {
        return [
            HttpRequestFromEnvironment::class => function () { return HttpRequestFromEnvironment::getRequestFromEnvironment(); },
            Http::class                       => function () { return HttpRequest::init(); },
        ];
    }
    protected function getResponseResolutionMethods() {
        return [
            Http::class => function () { return new HttpResponse(); },
        ];
    }
    protected function getResponseDispatchMethods() {
        return [
            Http::class =>
                function ($result) {
                    if ($result instanceof Request) {
                        throw new UnimplementedError("Can't do this kind of request yet!");
                    }
                    if (!($result instanceof HttpResponse)) {
                        $result = HttpResponse::init()->setBody(StringResolvable::init($result));
                    }
    
                    $result->makeHeaders();
    
                    echo $result->getBody();
                },
        ];
    }
    
}