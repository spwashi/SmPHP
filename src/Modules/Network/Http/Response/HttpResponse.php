<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 6:40 PM
 */

namespace Sm\Modules\Network\Http\Response;


use Sm\Communication\Response\AbstractResponse;

/**
 * Class HttpResponse
 *
 * Represents a Response that would be given to an HTTP-communicating thing
 *
 * @package Sm\Modules\Network\Http
 */
class HttpResponse extends AbstractResponse {
    public    $body;
    protected $headers;
    public function setContentType(string $content_type = 'text/html') {
        $this->headers['Content-Type'] = $content_type;
        return $this;
    }
    /**
     * Make the Headers that are referenced in this HttpResponse
     */
    public function makeHeaders() {
        if (isset($this->headers)) {
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }
    }
    
    /**
     * Get the Body of the Response
     *
     * @return mixed
     */
    public function getBody() {
        return $this->body;
    }
    /**
     * Set the Body of the Response
     *
     * @param mixed $body
     *
     * @return HttpResponse
     */
    public function setBody($body) {
        $this->body = $body;
        return $this;
    }
}