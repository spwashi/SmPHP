<?php


namespace Sm\Communication\Request;


/**
 * Class NamedRequest
 *
 * Represents a Request for a Route that has a name w/r to some router
 *
 */
class NamedRequest extends Request implements InternalRequest {
    protected $name;
    /** @var array An array of parameters that we are going to provide to the route ( somehow ) */
    protected $parameters = [];
    function jsonSerialize() {
        return [ 'name' => $this->name, ];
    }
    public function getName():?string {
        return $this->name;
    }
    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }
}