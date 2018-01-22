<?php


namespace Sm\Modules\Sql\Data\Model;


use Sm\Core\Exception\UnimplementedError;

/**
 * Class Model
 *
 *
 */
class Model extends \Sm\Data\Model\Model {
    public function save() {
        throw new UnimplementedError("Cannot save yet");
    }
    
    public static function create() {
        throw new UnimplementedError("Cannot create models yet");
    }
}