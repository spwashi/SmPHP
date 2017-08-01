<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 6:20 PM
 */

namespace Sm\Communication\Network\Http\Request;

/**
 * Class HttpRequestFromEnvironment
 *
 * Just meant to make it easier to get Request information from the Environment
 *
 * @package Sm\Communication\Network\Http
 */
class HttpRequestFromEnvironment extends HttpRequest {
    /**
     * Get the URL of however we entered
     *
     * @return string
     */
    public static function getEnvironmentRequestURL() {
        $host        = $_SERVER['HTTP_HOST']??'';
        $request_uri = $_SERVER['REQUEST_URI']??'';
        return "//{$host}{$request_uri}";
    }
    /**
     * Get the Request Method that was used to make the request initially
     *
     * @return string
     */
    public static function getEnvironmentRequestMethod() {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    /**
     * @return string
     */
    public static function getEnvironmentRequestedContentType() {
        return static::getEnvironmentRequestMethod() === 'get' ? null : $_SERVER["CONTENT_TYPE"];
    }
    /**
     * Initialize an HttpRequest from the Environment
     *
     * @return \Sm\Communication\Network\Http\Request\HttpRequest
     */
    public static function getRequestFromEnvironment() {
        return static::init(static::getEnvironmentRequestURL())
                     ->setRequestMethod(static::getEnvironmentRequestMethod())
                     ->setRequestedContentType(static::getEnvironmentRequestedContentType());
    }
}