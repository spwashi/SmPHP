<?php
/**
 * User: Sam Washington
 * Date: 1/28/17
 * Time: 2:38 AM
 */

namespace Sm\Communication\Routing;


use Sm\Communication\Network\Http\Request\HttpRequestDescriptor;
use Sm\Communication\Request\InternalRequest;
use Sm\Communication\Request\Request;
use Sm\Communication\Request\RequestDescriptor;
use Sm\Core\Exception\Exception;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\TypeMismatchException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\AbstractResolvable;
use Sm\Core\Resolvable\Error\UnresolvableException;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Resolvable\ResolvableFactory;

class Route extends FunctionResolvable {
    /** @var  AbstractResolvable $backupResolvable */
    protected $backupResolvable;
    /** @var  AbstractResolvable $subject */
    protected $subject;
    protected $pattern;
    /** @var  RequestDescriptor $requestDescriptor */
    protected $requestDescriptor;
    /** @var array An array pf */
    protected $parameters = [];
    
    ####################################################
    #   Initializers
    ####################################################
    public function __construct($resolution, $requestDescriptor = null, $backup = null) {
        if ($requestDescriptor instanceof RequestDescriptor) {
            $this->setRequestDescriptor($requestDescriptor);
        } else if (class_exists(HttpRequestDescriptor::class) && is_scalar($requestDescriptor)) {
            $this->setRequestDescriptor(new HttpRequestDescriptor($requestDescriptor));
        } else if (isset($requestDescriptor)) {
            # Trying to register something that isn't a string (when we have the Http module) and isn't a RequestDescriptor
            throw new UnimplementedError("Cannot accept RequestDescriptors that aren't RequestDescriptors ");
        }
        
        if ($backup instanceof Resolvable) $this->setBackup($backup);
    
        /** @var Resolvable $resolution */
        $resolution = $this->standardizeResolution($resolution);
        parent::__construct($resolution);
    }
    public static function init($resolution = null, $pattern = null, $default = null) {
        if (!isset($resolution)) {
            throw new InvalidArgumentException("Cannot initialize route without a resolution");
        }
        if ($resolution instanceof Route) return $resolution;
    
        $Route = new static($resolution, $pattern, $default);
        return $Route;
    }
    function __debugInfo() {
        return parent::__debugInfo() + [
                'pattern' => $this->requestDescriptor,
            ];
    }
    ####################################################
    #   Resolution
    ####################################################
    /**
     * Check to see if two Requests match.
     *
     * Internal Requests are assumed to math this Route. #todo reconsider?
     *
     * @param \Sm\Communication\Request\Request $request
     *
     * @return bool
     */
    public function matches(Request $request) {
        try {
            if ($request instanceof InternalRequest) return true;
            if (!isset($this->requestDescriptor)) return false;
            $this->requestDescriptor->compare($request);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    /**
     * Resolve the Route.
     *
     * @param \Sm\Communication\Routing\RequestContext      $request ,..
     *
     * @param \Sm\Communication\Routing\RequestContext|null $routeResolutionContext
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\TypeMismatchException
     * @throws \Sm\Core\Resolvable\Error\UnresolvableException
     */
    public function resolve($request = null, RequestContext $routeResolutionContext = null) {
        if (!($request instanceof Request)) throw new InvalidArgumentException('Can only route requests');
        if (!($this->subject instanceof Resolvable)) throw new UnresolvableException("No way to resolve request.");
        
        $routeResolutionContext = $routeResolutionContext ?? RequestContext::init($request);
        
        try {
            if ($this->matches($request)) {
                $arguments = $this->requestDescriptor ? $this->requestDescriptor->getArguments($request) : [];
                $arguments = array_merge([ $routeResolutionContext ], $arguments);
                return $this->subject->resolve(...array_values($arguments));
            }
    
            throw new UnresolvableException("Cannot match route with this request");
    
        } catch (UnresolvableException|InvalidArgumentException $e) {
            if (isset($this->backupResolvable)) return $this->backupResolvable->resolve($routeResolutionContext);
            throw $e;
        } catch (TypeMismatchException $e) {
            if (isset($this->backupResolvable)) return $this->backupResolvable->resolve($request);
            throw $e;
        }
    }
    ####################################################
    #   Setters/Getters
    ####################################################
    /**
     * Set the Resolvable that is to be used in case this function conks out
     *
     * @param \Sm\Core\Resolvable\AbstractResolvable|\Sm\Core\Resolvable\Resolvable $DefaultResolvable
     *
     * @return \Sm\Communication\Routing\Route
     */
    public function setBackup(Resolvable $DefaultResolvable): Route {
        $this->backupResolvable = $DefaultResolvable;
        return $this;
    }
    public function getRequestDescriptor(): ?RequestDescriptor {
        return $this->requestDescriptor;
    }
    /**
     * Set the Descriptor we'll use to see if a Request matches
     *
     * @param RequestDescriptor $requestDescriptor
     *
     * @return $this
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function setRequestDescriptor(RequestDescriptor $requestDescriptor) {
        $this->requestDescriptor = $requestDescriptor;
        return $this;
    }
    /**
     * Make sure the Resolvable is the way we want it
     *
     * @param $resolution
     *
     * @return  Resolvable
     */
    protected function standardizeResolution($resolution) {
        if (is_string($resolution) && strpos($resolution, '::') !== false) {
            $self          = $this;
            $resolveMethod =
                function ($Request = null) use ($resolution, $self) {
                    $resolution_expl = explode('::', $resolution);
                    $class_name      = $resolution_expl[0];
                    $method_name     = $resolution_expl[1] ?? null;
                    
                    # If the class doesn't have the requested method, skip it
    
    
                    $self->checkClassMethod($class_name, $method_name);
                    
                    $resolution = [ new $class_name, $method_name, ];
                    return FunctionResolvable::init($resolution)->resolve(...func_get_args());
                };
            
            $resolution = FunctionResolvable::init($resolveMethod);
        } else {
            $resolution = ResolvableFactory::init()->build($resolution);
        }
        return $resolution;
    }
    private function checkClassMethod($class_name, $method_name) {
        if (!$class_name || !$method_name) {
            throw new UnresolvableException("Incomplete definition");
        } else if (!class_exists($class_name)) {
            throw new UnresolvableException("No class {$class_name}");
        } else if (!method_exists($class_name, $method_name)) {
            throw new UnresolvableException("Method '{$method_name}' not found on '{$class_name}'");
        }
    }
    /**
     * @param array $parameters
     *
     * @return Route
     */
    public function setParameters(array $parameters): Route {
        $this->parameters = $parameters;
        return $this;
    }
}