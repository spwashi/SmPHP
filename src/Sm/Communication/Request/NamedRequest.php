<?php


namespace Sm\Communication\Request;


class NamedRequest extends Request {
    protected $name;
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