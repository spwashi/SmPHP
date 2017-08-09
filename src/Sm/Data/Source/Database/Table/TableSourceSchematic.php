<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 10:11 PM
 */

namespace Sm\Data\Source\Database\Table;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\SmEntity\StdSmEntitySchematicTrait;
use Sm\Data\Source\DataSourceSchematic;

/**
 * Class TableSourceSchematic
 *
 * Class used to describe a TableSource
 *
 * @package Sm\Data\Source\Database\Table
 */
class TableSourceSchematic implements TableSourceSchema, DataSourceSchematic, \JsonSerializable {
    use HasObjectIdentityTrait;
    use StdSmEntitySchematicTrait {
        load as protected _load_std;
    }

    public function __construct(string $name = null) {
        if ($name) $this->setName($name);
        $this->createSelfID();
    }
    public static function init() {
        return new static(...func_get_args());
    }
    public function load($configuration) {
        $this->_load_std($configuration);
        $this->_configArraySet__name($configuration);
        return $this;
    }
    function jsonSerialize() {
        return [
            'smID' => $this->getSmID(),
            'name' => $this->getName(),
        ];
    }
    
}