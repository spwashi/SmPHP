<?php


namespace Sm\Core\SmEntity\Traits;


use Sm\Core\Exception\UnimplementedError;

trait Is_StdSmEntitySchematicTrait {
    use Is_StdSmEntityTrait;
    public function load($configuration) {
        if (!is_array($configuration)) {
            throw new UnimplementedError("Cannot configure schematic using something other than an array");
        }
        
        $this->_configArraySet__smID($configuration);
        $this->_configArraySet__name($configuration);
        return $this;
    }
    public static function getNameFromSmID($smID) {
        $regex = '(^\[[a-zA-Z_]+])(.+)'; # [Type]name   e.g.   [Model]users
        preg_match("~{$regex}~", $smID, $matches);
        
        # return the name if there is one, otherwise return what's used as the "type"
        return $matches[2] ?? $smID;
    }
    
    #
    ##  Configuration
    /**
     * Provided a configuration array, get the name represented by it
     *
     * @param $configuration
     *
     */
    protected function _configArraySet__name($configuration) {
        $name = $configuration['name'] ?? static::getNameFromSmID($this->_smID) ?? null;
        if (isset($name)) $this->setName($name);
    }
    /**
     * set the smID of this SmEntity from the Configuration array
     *
     * @param $configuration
     */
    protected function _configArraySet__smID($configuration) {
        $this->_smID = $configuration['smID'] ?? $this->_smID ?? null;;
    }
}