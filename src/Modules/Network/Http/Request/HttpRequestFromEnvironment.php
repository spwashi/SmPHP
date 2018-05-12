<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 6:20 PM
 */

namespace Sm\Modules\Network\Http\Request;

/**
 * Class HttpRequestFromEnvironment
 *
 * Just meant to make it easier to get Request information from the Environment
 *
 * @package Sm\Modules\Network\Http
 */
class HttpRequestFromEnvironment extends HttpRequest {
    /**
     * Get the URL of however we entered
     *
     * @return string
     */
    public static function getEnvironmentRequestURL() {
        $host        = $_SERVER['HTTP_HOST'] ?? '';
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
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
    public static function getRequestData() {
        $contentType = static::getEnvironmentRequestedContentType();
        
        $request_data    = array_merge([], $_POST);
        $contentType_arr = $contentType ? (explode(';', $contentType)) : [];
        if (($contentType_arr[0] ?? '') === 'application/json') {
            $rawData = file_get_contents("php://input");
            $decode       = json_decode($rawData, true) ?: [];
            $request_data = array_merge($request_data, $decode);
        }
        
        return $request_data;
    }
    /**
     * @return string
     */
    public static function getEnvironmentRequestedContentType() {
        return static::getEnvironmentRequestMethod() === 'get' ? null : $_SERVER["CONTENT_TYPE"] ?? null;
    }
    /**
     * Initialize an HttpRequest from the Environment
     *
     * @return \Sm\Modules\Network\Http\Request\HttpRequest
     */
    public static function getRequestFromEnvironment() {
        return static::init(static::getEnvironmentRequestURL())
                     ->setRequestMethod(static::getEnvironmentRequestMethod())
                     ->setRequestedContentType(static::getEnvironmentRequestedContentType());
    }
}