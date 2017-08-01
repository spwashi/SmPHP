<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 8:28 PM
 */

namespace Sm\Communication\Network\Http\Request;


use Sm\Communication\Request\Request;
use Sm\Communication\Request\RequestDescriptor;
use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Exception\InvalidArgumentException;

class HttpRequestDescriptor extends RequestDescriptor {
    protected $matching_context_classes = [ HttpRequest::class ];
    protected $url_path_pattern;
    /** @var array These are the things that we want to identify as a parameter to the Route (made available to the route) */
    protected $argument_names = [];
    
    
    /**
     * HttpRequestDescriptor constructor.
     *
     * @param string $url_path A regex that will be used to match Requests
     */
    public function __construct($url_path = null) {
        if (isset($url_path)) $this->setMatchingUrlPattern($url_path);
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
            $this->check_matching_url_path($request);
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
        $this->setStringPattern($url_path_pattern);
        return $this;
    }
    public function getArguments(Request $request) {
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
                
                # We only want the regex
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
            $resultant_url_pattern .= '(?:/|$)   ';
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
    protected function check_matching_url_path(HttpRequest $request) {
        $request_path = $request->getUrlPath();
        if (!isset($this->url_path_pattern) || !is_string($request_path)) throw new InvalidArgumentException("Configured route is not a string");
        
        
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
}