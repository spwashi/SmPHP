<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 10:11 PM
 */

namespace Sm\Data\Source\Database\Table;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;

/**
 * Class TableSourceSchematic
 *
 * Class used to describe a TableSource
 *
 * @package Sm\Data\Source\Database\Table
 */
class TableSourceSchematic implements TableSourceSchema {
    use HasObjectIdentityTrait;
    protected $name;
    public function __construct(string $name = null) {
        if ($name) $this->setName($name);
        $this->createSelfID();
    }
    public static function init() {
        return new static(...func_get_args());
    }
    public function getName(): ?string {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
}