<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 8:28 PM
 */

namespace Sm\Modules\Network\Http\Request;


use Sm\Communication\Request\NamedRequest;
use Sm\Communication\Request\Request;
use Sm\Communication\Request\RequestDescriptor;
use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Util;

class HttpRequestDescriptor extends RequestDescriptor {
    protected $url_path_pattern;
    protected $matching_http_methods;
    protected $original_url_path;
    /** @var array These are the things that we want to identify as a parameter to the Route (made available to the route) */
    protected $argument_names = [];
    
    
    /**
     * HttpRequestDescriptor constructor.
     *
     * @param string $pattern A regex that will be used to match Requests
     */
    public function __construct($pattern = null) {
        $path        = false;
        $http_method = null;
        if (is_array($pattern)) {
            $path        = $pattern['path'] ?? $path;
            $http_method = $pattern['http_method'] ?? $http_method;
        } else if (is_string($pattern)) {
            $path = $pattern;
        }
        if (isset($path)) $this->setMatchingUrlPattern($path);
        if (isset($http_method)) $this->setMatchingHttpMethodPattern($http_method);
    }
    
    /**
     * @param \Sm\Core\Context\Context $request
     *
     * @throws \Sm\Core\Context\Exception\InvalidContextException
     */
    public function compare($request) {
        /** @var HttpRequest $request */
        parent::compare($request);
        if (!($request instanceof HttpRequest)) throw new InvalidContextException("Expected an HttpRequest");
        
        if (isset($this->url_path_pattern)) {
            $this->checkHttpRequestMatches($request);
        }
    }
    
    
    /**
     * Set the URL Path that we want to match
     *
     * @param $url_path_pattern
     *
     * @return $this
     */
    public function setMatchingUrlPattern($url_path_pattern) {
        $this->original_url_path = $url_path_pattern;
        $this->setStringPattern($url_path_pattern);
        return $this;
    }
    public function setMatchingHttpMethodPattern(array $http_method_pattern) {
        $this->matching_http_methods = $http_method_pattern;
        return $this;
    }
    
    /**
     * Create a URL path from this provided some arguments
     *
     * @param $arguments
     *
     * @return string
     */
    public function asUrlPath($arguments = null): string {
        $this->checkHttpRequestArguments($arguments);
        $argument_names = $this->argument_names;
        $expl           = explode('/',
                                  ltrim(trim($this->original_url_path, '/'), '/'));
        $end_url_arr    = [];
        foreach ($expl as $url_part) {
            if (($url_part[0] ?? 0) === '{') { #  This is an argument to the URL
                $_arg_name     = array_shift($argument_names);
                $end_url_arr[] = $this->getArgumentForUrl($_arg_name, $arguments);
            } else {
                $end_url_arr[] = $url_part;
            }
        }
        
        return '/' . implode('/', $end_url_arr);
    }
    public function getArguments(Request $request = null) {
        if (empty($this->argument_names)) return [];
        
        if ($request instanceof NamedRequest) return $request->getParameters();
        if (!($request instanceof HttpRequest)) throw new InvalidArgumentException("Expected an HttpRequest");
        
        return $this->getArgumentsFromUrlPathString($request->getUrlPath());
    }
    
    /**
     * This is a setter used when we want to set the "pattern" of the class to be a string.
     * This has to be used with a URL-like route pattern.
     *
     * examples:
     *  spwashi/{param_1}:[a-zA-Z_]+
     *  spwashi$
     *  spwashi/
     *
     * @param string $configured_url_pattern
     *
     * @return string
     */
    protected function setStringPattern(string $configured_url_pattern) {
        $configured_url_pattern = trim($configured_url_pattern, ' \\/'); # remove the end
        
        # Explode the url pattern to iterate & normalize it, also get the parameters to the route
        $url_pattern_arr = explode('/', $configured_url_pattern);
        
        /** @var string $resultant_url_pattern The end pattern that we want to match the URL against */
        $resultant_url_pattern = '';
        foreach ($url_pattern_arr as $index => $url_pattern_segment) {
            $last_char = substr($url_pattern_segment, -1);
            
            # Regex to match like   {parameter_name}
            #                 or    {parameter_name}:[a-zA-Z_\d]+
            
            preg_match("`\\{(.+)\\}:?(.+)?(/|$)`", $url_pattern_segment, $match_container_arr);
            
            $parameter_name = $match_container_arr[1] ?? null;
            
            # $match[1] is the parameter_name
            if (isset($parameter_name)) {
                
                # Add the parameter name to the list of parameters so we can get the matches in order
                $this->argument_names[] = $parameter_name;
                
                # We only want the regex, default to alpha or underscore followed by anything (because these might be methods?)
                $url_pattern_segment = !empty($match_container_arr[2]) ? $match_container_arr[2] : '[a-zA-Z_]+[a-zA-Z_\d]*';
                
                # Wrap it in parentheses so we can get the value
                $url_pattern_segment = "($url_pattern_segment)";
            }
            
            # If this isn't the first one, match either the pattern segment regex, end of line, or /
            # todo does this need to be different for the first one vs last one?
            if ($index !== 0 && $last_char === '*') {
                $url_pattern_segment = "(?:$|{$url_pattern_segment}|/?$) ";
            }
            
            $resultant_url_pattern .= $url_pattern_segment; # If it's the first one, set the resultant url pattern
            
            #match / or end of line
            $resultant_url_pattern .= '(?:/|$)' . '';
        }
        return $this->url_path_pattern = $resultant_url_pattern;
    }
    
    /**
     * Check to see if the URL path matches what was asked for.
     * todo consider adding multiple url paths?
     *
     * @param HttpRequest $request
     *
     * @throws \Sm\Core\Context\Exception\InvalidContextException
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function checkHttpRequestMatches(HttpRequest $request) {
        $request_path = $request->getUrlPath();
        if (!isset($this->url_path_pattern) || !is_string($request_path)) throw new InvalidArgumentException("Configured route is not a string");
        
        $methods = $this->matching_http_methods;
        if (is_array($methods) && !in_array(strtolower($request->getRequestMethod()), $methods)) {
            throw new InvalidContextException("HTTP Method does not match");
        }
        
        # exact matches work
        # todo don't exactly match regexes?
        if ($request_path === $this->url_path_pattern) return;
        
        if (is_string($this->url_path_pattern)) {
            # Check to see if the pattern matches
            preg_match("~^{$this->url_path_pattern}~x", $request_path, $matches);
            if (!empty($matches)) return;
        }
        
        # Does not match
        throw new InvalidContextException("Request does not match descriptor"); #todo more desriptive
    }
    
    /**
     * Get an array of things we'll use as arguments to (usually routes)
     *
     * @param string $path_string
     *
     * @return array
     */
    private function getArgumentsFromUrlPathString(string $path_string) {
        preg_match("~^{$this->url_path_pattern}~x", $path_string, $matches);
        
        # If we couldn't find any matches, return an empty array
        if (!count($matches)) return [];
        
        # Don't care about the text that matched the full pattern
        array_shift($matches);
        $arguments = [];
        
        # Iterate through the argument names to get the matching value
        foreach ($this->argument_names as $argument_name) {
            # Continue if there are no more matching arguments
            if (!count($matches)) continue;
            
            # Get the value from the regexp matches
            $parameter_value = array_shift($matches);
            
            # If there is an argument name (would there not be?)
            $arguments[ $argument_name ] = $parameter_value;
        }
        
        # If there is anything else in the "matches" array, also include that. Not sure why #todo
        return array_merge($arguments, $matches);
    }
    /**
     * Check to make sure the arguments that we are using to create a URL are going to work
     *
     * @param $arguments
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function checkHttpRequestArguments($arguments = null) {
        $arguments = $arguments ?? [];
        
        if (!is_array($arguments)) throw new InvalidArgumentException("Can only accept associative arrays");
        foreach ($this->argument_names as $argument_name) {
            if (!isset($arguments[ $argument_name ])) throw new InvalidArgumentException("The '{$argument_name}' argument is missing'");
        }
    }
    /**
     * Get an argument name from a provided variable of arguments that we are going to inject into the URL we're creating
     *
     * @param $_arg_name
     * @param $arguments
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    protected function getArgumentForUrl($_arg_name, $arguments): string {
        
        if (!isset($_arg_name)) throw new InvalidArgumentException("No argument name was provided!");
        
        # Right now only associative arrays work
        if (is_array($arguments) && isset($arguments[ $_arg_name ])) {
            $arg = $arguments[ $_arg_name ];
            if (!Util::canBeString($arg)) throw new UnimplementedError("Cannot get Arguments from " . Util::getShape($arg));
            return $arg;
        }
        
        throw new UnimplementedError("Can only get arguments from a string");
    }
    
    public function __debugInfo() {
        return $this->jsonSerialize();
    }
    public function jsonSerialize() {
        return [
            'pattern'      => $this->url_path_pattern,
            'original'     => $this->original_url_path,
            'arguments'    => $this->argument_names,
            'http_methods' => $this->matching_http_methods,
        ];
    }
}